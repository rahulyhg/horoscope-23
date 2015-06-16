<?php header('Content-Type: text/html; charset=utf-8');?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title></title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php 
$name = [
	"aries"=>"овна",
	"taurus"=>"тельца",
	"gemini"=>"близнецов",
	"cancer"=>"рака",
	"leo"=>"льва",
	"virgo"=>"девы",
	"libra"=>"весов",
	"scorpio"=>"скорпиона",
	"sagittarius"=>"стрелеца",
	"capricorn"=>"козерога",
	"aquarius"=>"водолея",
	"pisces"=>"рыб"]; 
	$goro = array();

	if($_SERVER['REQUEST_METHOD']=="POST" and !empty($_POST['day'])){
	if($_POST['day']=="yestarday"){
		$day = date('d')-1;
		$date = $day.date('.m.Y');
	}elseif($_POST['day']=="today"){
		$date = date('d.m.Y');
	}elseif($_POST['day']=="tomorrow"){
		$day = date('d')+1;
		$date = $day.date('.m.Y');
	}

}else{
	$day = date('d');
	$date = $day.date('.m.Y');
}

$memcache = new Memcache;
$memcache -> connect("localhost",11211);

if(!$memcache->get("$date")){
$xml = simplexml_load_file("http://img.ignio.com/r/export/utf/xml/daily/com.xml");
	$yestarday = $xml->date['yesterday'];
	$today = $xml->date['today'];
	$tomorrow = $xml->date['tomorrow'];
	foreach($name as $znak => $values){
	$yesterdaytext = $xml->$znak->yesterday;
	$todaytext = $xml->$znak->today;
	$tomorrowtext = $xml->$znak->tomorrow;	
	$goro["$yestarday"]["$znak"] = "$yesterdaytext";
	$goro["$today"]["$znak"] = "$todaytext";
	$goro["$tomorrow"]["$znak"] = "$tomorrowtext";
}
foreach($goro as $key => $val){
$memcache -> set($key,$val);
}	
$mas = $memcache->get("$date"); 
}else{
$mas = $memcache->get("$date"); 
}


$xml = simplexml_load_file("http://img.ignio.com/r/export/utf/xml/daily/com.xml");
foreach($mas as $key => $val){

	echo mb_strtoupper("<h2 id='",'utf-8').strtolower("$key").mb_strtoupper("'>ГОРОСКОП ДЛЯ $name[$key] на $date</h2>",'utf-8');
	echo "<p>$val</p>";?>
<form action='<?php echo  $_SERVER['PHP_SELF']."#$key" ?>' method="post">
<input type="submit" name="day" value="yestarday">
<input type="submit" name="day" value="today">
<input type="submit" name="day" value="tomorrow">
</form>

<?php } ?>

</body>
</html>
