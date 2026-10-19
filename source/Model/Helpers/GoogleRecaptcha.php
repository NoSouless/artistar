<?php

namespace Source\Model\Helpers;

class GoogleRecaptcha {

    public static function verify($response, $expectedAction) {
        if (!defined('RECAPTCHA_ENABLED') || !constant('RECAPTCHA_ENABLED') || empty($response) || !function_exists('curl_init')) return false;

        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => constant('RECAPTCHA_SECRET_KEY'),
                'response' => $response,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8
        ]);

        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($result === false || $status !== 200) return false;

        $verification = json_decode($result, true);
        $requestHost = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
        return is_array($verification)
            && !empty($verification['success'])
            && ($verification['action'] ?? null) === $expectedAction
            && strtolower($verification['hostname'] ?? '') === $requestHost
            && (float) ($verification['score'] ?? 0) >= 0.5;
    }
}