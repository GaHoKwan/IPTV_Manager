<?php

function loc($ip) {
    retry:
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip=$ip&accessKey=alibaba-inc";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 2);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    $curljson = curl_exec($curl);
    curl_close($curl);
    $curlobj = strtr($curljson, array(
        "X" => ''
    ));
    $ipobj = json_decode($curlobj);
    if (empty($ipobj->data)) {
        $ipobj = (Object)null;
        goto retry;
    }
    $country = $ipobj->data->country;
    $region = $ipobj->data->region;
    $city = $ipobj->data->city;
    $isp = $ipobj->data->isp;
    $obj = (Object)null;
    if ($region == '' && $city == '') {
        $region = $country;
    }
    if ($region == $city) {
        $region = $country;
    }
    if (empty($region)) {
        $region = $country;
    }
    if (empty($city)) {
        $region = $country;
        $city = $ipobj->data->region;
    }
    if (empty($isp)) {
        $region = $country;
        $city = '';
        $isp = $ipobj->data->region;
    }
    $obj->region = $region;
    $obj->city = $city . '，';
    $obj->isp = $isp;
    $json['data'] = $obj;
    return json_encode($json, JSON_UNESCAPED_UNICODE);
}

?>

