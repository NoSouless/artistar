<?php


namespace Source\Model\Settings;

use PDO;
use Source\Model\Helpers\PasswordChangedNotifier;
use Source\Model\Helpers\RememberMe;
use Source\Model\Settings;

class Security extends Settings {

    public function checkPassword($userId, $password) {
        $statement = $this->SQL->prepare('SELECT 1 FROM usuarios WHERE usuario_id = :id AND usuario_senha = :password');
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':password', md5($password), PDO::PARAM_STR);
        $statement->execute();
        return (bool) $statement->fetchColumn();
    }

    public function updatePassword($userId, $password) {
        $statement = $this->SQL->prepare('UPDATE usuarios SET usuario_senha = :password WHERE usuario_id = :id');
        $statement->bindValue(':password', md5($password), PDO::PARAM_STR);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        if (!$statement->execute()) return false;
        // Encerra os logins persistentes em todos os dispositivos
        (new RememberMe($this->SQL))->revokeAll($userId);
        PasswordChangedNotifier::notify($this->SQL, $userId);
        return true;
    }

    public function getDevices($userId) {
        $statement = $this->SQL->prepare('
            SELECT token_id, token_selector, token_user_agent, token_ip, token_criado_em, token_ultimo_uso, token_expira_em
            FROM usuarios_tokens
            WHERE token_usuario = :id AND token_expira_em > NOW()
            ORDER BY COALESCE(token_ultimo_uso, token_criado_em) DESC
        ');
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
        $currentSelector = RememberMe::currentSelector();
        $devices = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $row['current'] = $currentSelector !== null && hash_equals($row['token_selector'], $currentSelector);
            $row['description'] = self::describeUserAgent($row['token_user_agent'] ?? '');
            unset($row['token_selector']);
            $devices[] = $row;
        }
        return $devices;
    }

    public function revokeDevice($userId, $tokenId) {
        $statement = $this->SQL->prepare('DELETE FROM usuarios_tokens WHERE token_id = :token AND token_usuario = :id');
        $statement->bindValue(':token', $tokenId, PDO::PARAM_INT);
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() > 0;
    }

    public function revokeOtherDevices($userId) {
        $selector = RememberMe::currentSelector();
        $statement = $this->SQL->prepare('DELETE FROM usuarios_tokens WHERE token_usuario = :id AND token_selector <> :selector');
        $statement->bindValue(':id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':selector', $selector ?? '', PDO::PARAM_STR);
        return $statement->execute();
    }

    public static function describeUserAgent($ua) {
        $browser = 'Navegador desconhecido';
        foreach (['Edg' => 'Edge', 'OPR' => 'Opera', 'Firefox' => 'Firefox', 'Chrome' => 'Chrome', 'Safari' => 'Safari'] as $needle => $name) {
            if (stripos($ua, $needle) !== false) { $browser = $name; break; }
        }
        $os = 'Sistema desconhecido';
        foreach (['Windows' => 'Windows', 'Android' => 'Android', 'iPhone' => 'iOS', 'iPad' => 'iOS', 'Mac OS' => 'macOS', 'Linux' => 'Linux'] as $needle => $name) {
            if (stripos($ua, $needle) !== false) { $os = $name; break; }
        }
        return $browser . ' · ' . $os;
    }

}