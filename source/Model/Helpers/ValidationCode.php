<?php

namespace Source\Model\Helpers;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Source\Core\Core;

class ValidationCode extends Core {

    public function __construct() {
        $this->setEnableRedirect(false);
        parent::__construct();
    }

    public function searchEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $emailStatement = $this->SQL->prepare('
            SELECT
                usuario_id id,
                usuario_nome_completo nome,
                usuario_email email
            FROM
                usuarios 
            WHERE
                usuario_email = :sentEmail
        ');
        $emailStatement->bindParam(':sentEmail', $email, PDO::PARAM_STR);
        $emailStatement->execute();
        $result = $emailStatement->fetch();
        return $result;
    }

    public function searchUser($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $userStatement = $this->SQL->prepare('
            SELECT
                usuario_id id,
                usuario_nome_completo nome,
                usuario_email email
            FROM
                usuarios
            WHERE
                usuario_id = :id
        ');
        $userStatement->bindParam(':id', $id, PDO::PARAM_INT);
        $userStatement->execute();
        $result = $userStatement->fetch();
        return $result;
    }

    public function sendValidationEmail($email, $validationCode) {
        $credentials = EMAIL_CREDENTIALS;
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $translator = $this->getTranslator();
        try {
            $mail->isSMTP();
            $mail->Host = $credentials['host']; // SMTP server
            $mail->SMTPAuth = true;
            $mail->isHTML(true);
            $mail->CharSet = $credentials['char_set'];
            $mail->Subject = $translator->translate('Confirmação de Email - Artistar');
            //Use credentials
            $mail->Username = $credentials['user'];
            $mail->Password = $credentials['pass'];
            $mail->SMTPSecure = $credentials['smtp_secure'];
            $mail->Port = $credentials['port'];
            $mail->setFrom($credentials['from']);
            //End of credentials
            $mail->addAddress($email);
            $halfValidationCode = ceil(strlen($validationCode) / 2);
            $validationCode = substr($validationCode, 0, $halfValidationCode) . '-' . substr($validationCode, $halfValidationCode);
            $mail->FromName = $credentials['name'];
            $mail->Body = $translator->translate('Seu código de validação é: ') . $validationCode;
            $mail->AltBody = $translator->translate('Seu código de validação é: ') . $validationCode;
            $mail->send();
            return true;
        } catch (PHPMailerException $e) {
            return false;
        }
    }

    public function updateValidationCode($id, $code) {
        $updateStatement = $this->SQL->prepare('
            UPDATE 
                usuarios 
            SET 
                usuario_codigo_validacao = :code,
                usuario_envio_validacao = NOW()
            WHERE 
                usuario_id = :id
        ');
        $updateStatement->bindParam(':code', $code, PDO::PARAM_STR);
        $updateStatement->bindParam(':id', $id, PDO::PARAM_INT);
        return $updateStatement->execute();
    }

    public function validateCode($id, $code) {
        $codeStatement = $this->SQL->prepare('
            SELECT
                COUNT(*) qtd
            FROM
                usuarios
            WHERE
                usuario_id = :id 
            AND 
                usuario_codigo_validacao = :code
            AND
                usuario_envio_validacao > NOW() - INTERVAL 1 HOUR
        ');
        $codeStatement->bindParam(':id', $id, PDO::PARAM_INT);
        $codeStatement->bindParam(':code', $code, PDO::PARAM_STR);
        $codeStatement->execute();
        $result = $codeStatement->fetch();
        return $result['qtd'] == 1;
    }
    
    public function updateEmailValidationStatus($id) {
        $updateStatement = $this->SQL->prepare('
            UPDATE 
                usuarios
            SET 
                usuario_codigo_validacao = NULL,
                usuario_envio_validacao = NULL
            WHERE 
                usuario_id = :id
        ');
        $updateStatement->bindParam(':id', $id, PDO::PARAM_INT);
        return $updateStatement->execute();
    }

    public function generateValidationCode($length = 6) {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }

}