<?php
error_reporting(0);

$bin = $_GET['lista'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$bin = substr($bin, 0, 6));
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$add = curl_exec($ch);
$card = getStr($add, '"scheme":"', '",');
$type = getStr($add, '"type":"', '",');
$brand = getStr($add, 'brand":"', '"');
$prepaid = getStr($add, '"prepaid":', ',');
$country = getStr($add, '","name":"', '","');
$currency = getStr($add, '"currency":"', '",');
$bank = getStr($add, '"bank":{"name":"', '",');
$emoji = getStr($add, '"emoji":"', '",');


$Resultado = ['Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];

Transform($Resultado);

#FUNCIONES REQUERIDAS

function getStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function Transform($Resultado)
{
    foreach ($Resultado as $key => $value) {
        $Resultado[$key] = $value.'.';
    }
    print_r($Resultado);
}
