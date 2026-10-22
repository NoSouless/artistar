<?php

namespace Source\Model\Helpers;

use PDO;

/**
 * Login persistente ("manter conectado") com o padrão selector:validator.
 * O cookie guarda "selector:validator"; o banco guarda apenas o hash SHA-256 do validator.
 * A cada uso o token é rotacionado. Se o selector existir mas o validator não bater,
 * considera-se roubo de token e todos os dispositivos do usuário são deslogados.
 */
class RememberMe {
    const COOKIE_NAME = 'artistar_remember';
    const LIFETIME = 7776000; // 90 dias, renovados a cada uso

    private $SQL;

    public function __construct($SQL) {
        $this->SQL = $SQL;
    }

    public static function currentSelector() {
        $cookie = $_COOKIE[self::COOKIE_NAME] ?? '';
        if (is_string($cookie) && preg_match('/^([a-f0-9]{18}):[a-f0-9]{64}$/', $cookie, $m)) return $m[1];
        return null;
    }

    public function issue($userId) {
        $selector = bin2hex(random_bytes(9));
        $validator = bin2hex(random_bytes(32));
        $statement = $this->SQL->prepare('
            INSERT INTO 
                usuarios_tokens (
                    token_usuario, 
                    token_selector, 
                    token_hash, 
                    token_expira_em, 
                    token_user_agent, 
                    token_ip
                )
            VALUES (
                :user, 
                :selector, 
                :hash, 
                :expires, 
                :ua, 
                :ip
            )
        ');
        $statement->execute([
            ':user' => $userId,
            ':selector' => $selector,
            ':hash' => hash('sha256', $validator),
            ':expires' => date('Y-m-d H:i:s', time() + self::LIFETIME),
            ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ':ip' => substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
        ]);
        $this->setCookie($selector . ':' . $validator, time() + self::LIFETIME);
    }

    /** Retorna o id do usuário se o cookie for válido (e rotaciona o token), senão null. */
    public function consume() {
        $cookie = $_COOKIE[self::COOKIE_NAME] ?? '';
        if (!is_string($cookie) || !preg_match('/^[a-f0-9]{18}:[a-f0-9]{64}$/', $cookie)) {
            if ($cookie !== '') $this->clearCookie();
            return null;
        }
        [$selector, $validator] = explode(':', $cookie);

        $statement = $this->SQL->prepare('
            SELECT 
                token_id, 
                token_usuario, 
                token_hash, 
                token_expira_em 
            FROM 
                usuarios_tokens 
            WHERE 
                token_selector = :selector
        ');
        $statement->execute([':selector' => $selector]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $this->clearCookie();
            return null;
        }
        if (!hash_equals($row['token_hash'], hash('sha256', $validator))) {
            $this->revokeAll($row['token_usuario']);
            $this->clearCookie();
            return null;
        }
        if (strtotime($row['token_expira_em']) < time()) {
            $this->revoke();
            return null;
        }

        $newValidator = bin2hex(random_bytes(32));
        $expires = time() + self::LIFETIME;
        $update = $this->SQL->prepare('
            UPDATE 
                usuarios_tokens
            SET 
                token_hash = :hash, 
                token_expira_em = :expires, 
                token_ultimo_uso = NOW()
            WHERE 
                token_id = :id
        ');
        $update->execute([
            ':hash' => hash('sha256', $newValidator),
            ':expires' => date('Y-m-d H:i:s', $expires),
            ':id' => $row['token_id'],
        ]);
        $this->setCookie($selector . ':' . $newValidator, $expires);
        return (int) $row['token_usuario'];
    }

    /** Remove o token do dispositivo atual e apaga o cookie. */
    public function revoke() {
        $cookie = $_COOKIE[self::COOKIE_NAME] ?? '';
        if (is_string($cookie) && preg_match('/^([a-f0-9]{18}):/', $cookie, $m)) {
            $this->SQL->prepare('DELETE FROM usuarios_tokens WHERE token_selector = :selector')->execute([':selector' => $m[1]]);
        }
        $this->clearCookie();
    }

    public function revokeAll($userId) {
        $this->SQL->prepare('DELETE FROM usuarios_tokens WHERE token_usuario = :user')->execute([':user' => $userId]);
    }

    private function setCookie($value, $expires) {
        setcookie(self::COOKIE_NAME, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE_NAME] = $value;
    }

    private function clearCookie() {
        setcookie(self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        unset($_COOKIE[self::COOKIE_NAME]);
    }
}
