<?php

function getMonthName($monthNumber) {
    $months = [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
    return $months[$monthNumber] ?? '';
}

function formatWeekDateToPortuguese($date) {
    $daysOfWeek = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda-feira',
        'Tuesday' => 'Terça-feira',
        'Wednesday' => 'Quarta-feira',
        'Thursday' => 'Quinta-feira',
        'Friday' => 'Sexta-feira',
        'Saturday' => 'Sábado'
    ];
    $dayName = date('l', strtotime($date));
    return $daysOfWeek[$dayName] ?? '';
}

function moedaReal($value) {
    return number_format($value, 2, ',', '.');
}

function desconverteMoedaReal($value) {
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return $value;
}

function storageURL($file) {
    $storage = STORAGE_CONFIG;
    $bucketPublico = $storage['s3']['buckets']['publico']['url'];
    $bucketPrivado = $storage['s3']['buckets']['privado']['url'];
    $localPublico = $storage['local']['publico']['url'];
    $file = str_replace('{bucketPublico}', $bucketPublico, $file);
    $file = str_replace('{bucketPrivado}', $bucketPrivado, $file);
    $file = str_replace('{localPublico}', $localPublico, $file);
    return $file;
}

function echo_r($data, $exit = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($exit) exit;
}