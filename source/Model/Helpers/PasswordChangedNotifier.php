<?php

namespace Source\Model\Helpers;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class PasswordChangedNotifier {

    /** Avisa o dono da conta por e-mail. Falhas no envio não interrompem a troca de senha. */
    public static function notify($SQL, $userId) {
        try {
            $statement = $SQL->prepare('
                SELECT 
                    usuario_email email, 
                    usuario_nome_completo nome 
                FROM 
                    usuarios 
                WHERE 
                    usuario_id = :id
            ');
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            if (empty($user['email'])) return false;

            $translator = (new Translator($_SESSION['artistar']['language'] ?? 'pt-br'))->loadTranslation('settings/security');
            $credentials = EMAIL_CREDENTIALS;
            $name = htmlspecialchars((string) $user['nome'], ENT_QUOTES, 'UTF-8');
            $when = date('d/m/Y H:i');
            $ip = htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '', ENT_QUOTES, 'UTF-8');

            $text = $translator->translate('Sua senha do Artistar foi alterada em ') . $when . ' (IP ' . $ip . '). '
                . $translator->translate('Se não foi você, redefina sua senha imediatamente e entre em contato com o suporte.');

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $credentials['host'];
            $mail->SMTPAuth = true;
            $mail->isHTML(true);
            $mail->CharSet = $credentials['char_set'];
            $mail->Username = $credentials['user'];
            $mail->Password = $credentials['pass'];
            $mail->SMTPSecure = $credentials['smtp_secure'];
            $mail->Port = $credentials['port'];
            $mail->setFrom($credentials['from'], $credentials['name']);
            $mail->addAddress($user['email']);
            $mail->Subject = $translator->translate('Sua senha foi alterada - Artistar');
            $mail->Body = '<p>' . $translator->translate('Olá') . ', ' . $name . '.</p><p>' . $text . '</p>';
            $mail->AltBody = strip_tags($text);
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
