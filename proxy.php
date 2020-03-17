<?php
error_reporting(0);
header('Content-Type: text/json;charset=UTF-8');
if (isset($_GET['vid'])) {
	$vid=$_GET['vid'];

	if ($vid=='tvb') {
		$id=$_GET['id'];
		$info=file_get_contents("http://news.tvb.com/live/$id?is_hd");
		preg_match('/<source src="(.*?)"/i',$info,$sn);
		$playurl=$sn[1];
	    header('location:'.urldecode($playurl));
		exit;
	}

	if ($vid=='migu') {
		$id=$_GET['id'];
		$info=file_get_contents("http://www.miguvideo.com/gateway/playurl/v1/play/playurl?contId=$id&rateType=4");
		preg_match('/"url":"(.*?)"/i',$info,$sn);
		$playurl=$sn[1];
		$url=str_replace(array("\u002F"),'/',$playurl);
		header('location:'.urldecode($playurl));
		exit;
	}

	if ($vid=='utvhk') {
		$id=$_GET['id'];
		$info=file_get_contents("http://miguapi.utvhk.com:18083/clt/publish/resource/UTV_NEW/playData.jsp?contentId=$id&nodeId=$id&rate=5&playerType=4&objType=LIVE");
		preg_match('/"url": "(.*?)"/i',$info,$sn);
		$playurl=$sn[1];
		$url=str_replace(array("\u002F"),'/',$playurl);
		header('location:'.urldecode($playurl));
		exit;
	}

	if ($vid=='iptv805') {
		$id=$_GET['id'];
		$part=$_GET['p'];
		$tid=$_GET['tid'];
		$url="http://m.iptv805.com/iptv.php?act=play&tid=$tid&id=$id". $part;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 8.0.0; Pixel 2 XL Build/OPD1.170816.004) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Mobile Safari/537.36');
		$curlobj = curl_exec($curl);
		preg_match('/<option value="(.*?)"/i',$curlobj,$sn);
		$playurl=$sn[1];
        $playurl = preg_replace('#http://m.iptv.com/player.m3u8#', 'http://play.ggiptv.com:13164/play.m3u8', $playurl);
		header('location:'.urldecode($playurl));
		exit;
	}

} else {
	header('HTTP/1.1 403 Forbidden');
	exit();
}

?>