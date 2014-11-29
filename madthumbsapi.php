<?
header('content-type: application/json; charset=utf-8');
//require("lisanssorgula.php");
$bot="Madthumbs";
$filename = "%%-".md5($_SERVER['PHP_SELF'].$_SERVER['REQUEST_URI'])."-%%.json";
$cachefile = "cache/".$bot.'/'.$filename;
$cachetime = 3 * 60 * 60; // Cache Süresi
//$cachetime = 0; // Cache Süresi
if (file_exists($cachefile))
{
if(time() - $cachetime < filemtime($cachefile))
{
readfile($cachefile);
exit;
}
else
{
unlink($cachefile);
}
}
ob_start();
//if(!defined("tamam")){exit("sie");}
$sayfa=$_GET['sayfa'];if(!$sayfa){$sayfa=1;}
if($_GET['arama'] or $_GET['kategori'] or $_GET['yeni']){
$bilgiler["videolar"];
$kategori=$_GET['kategori'];
$arama=$_GET['arama'];
//$harf=iconv("utf-8","windows-1254",$harf);
$ekleson="";

if($arama){
$baglanti=baglan("http://anonymouse.org/cgi-bin/anon-www.cgi/http://www.madthumbs.com/search?q=".urlencode($arama).'&p='.$sayfa);	

}else{

$yapi='|<li class="thumbbox".*<a href="http://www.madthumbs.com/videos/(.*?)" class="thumb_click" rev=.*><img.*class="scrub_thumb" src="(.*?)" longdesc=.*></a> <a.*</div>.*<h1 class="mtitle" title="(.*?)"><a.*>.*</a></h1> <div class="clear"> <span class="al fl">(.*?)</span>|';

$baglanti=baglan("http://anonymouse.org/cgi-bin/anon-www.cgi/http://www.madthumbs.com/categories/".$kategori."?p=".$sayfa);	
}

$anabaglanti=$baglanti;
$baglanti=str_replace('http://anonymouse.org/cgi-bin/anon-www.cgi/','',$baglanti);
$baglanti= preg_replace('/\s+/',' ',$baglanti);
$baglanti= str_replace('<li class="thumbbox"','

<li class="thumbbox"',$baglanti);
$baglanti= str_replace('<span class="freeviews','

<span class="freeviews',$baglanti);

$yapi='|<li class="thumbbox".*<a href="http://www.madthumbs.com/videos/(.*?)" class="thumb_click" rev=.*><img.*class="scrub_thumb" src="(.*?)" longdesc=.*></a> <a.*</div>.*<h1 class="mtitle" title="(.*?)"><a.*>.*</a></h1> <div class="clear"> <span class="al fl">(.*?)</span>|';


preg_match_all($yapi,$baglanti,$verop);
preg_match('|<li class="page_next_set"><a.*href=.*>(.*)</a></li>|',str_Replace('</li>','</li>

',$baglanti),$bgver);
if($bgver[1]){
$bilgiler["toplamsayfa"]=trim($bgver[1]);
}else{
$bilgiler["toplamsayfa"]=1;
}
for($i=0;$i<count($verop[1]);$i++){
	$id=$verop[1][$i];	
$bilgiler["videolar"][$i]["videoID"]=trim(strip_tags($id));
$isim=$verop[3][$i];	
$bilgiler["videolar"][$i]["isim"]=trim(strip_tags($isim));
$duration=$verop[4][$i];		
$bilgiler["videolar"][$i]["dakika"]=trim(strip_tags($duration));
$thumbs=$vidim->video->thumbs;	
$resim=$verop[2][$i];
$resim=str_replace('.jpg','',$resim);
$bolresim=end(explode('-',$resim));
$resimsekli=str_replace("-".$bolresim,'',$resim);
$resim=$resimsekli."-L-20.jpg";
$bilgiler["videolar"][$i]["resim"]=trim(strip_tags($resim));

for($thumbtm=1;$thumbtm<18;$thumbtm++){
$bilgiler["videolar"][$i]["tumresimler"][$thumbtm]=$resim=$resimsekli."-L-".($thumbtm*2).".jpg";


}

}

$data=json_encode($bilgiler);

echo $data;

$fp = fopen($cachefile, 'w+');
fwrite($fp, ob_get_contents());
fclose($fp);
ob_end_flush();

exit;
}



if($_GET['kategoriver']){
	
$bilgiler["kategoriler"];
$baglanti=baglan("http://www.madthumbs.com/");
$bilgiler["botadi"]=$bot;
preg_match_all('|<a title="(.*?)" href="/categories/(.*?)".*>(.*?)</a>|',$baglanti,$bilgiver);

for($i=0;$i<count($bilgiver[1]);$i++){
		$sarkiID=$bilgiver[1][$i];
		$bilgiler["kategoriler"][$i]['id']=trim(strip_tags($bilgiver[2][$i]));
		$bilgiler["kategoriler"][$i]['name']=trim(strip_tags($bilgiver[3][$i]));		
					

	}

	$data=json_encode($bilgiler);
	echo $data;

$fp = fopen($cachefile, 'w+');
fwrite($fp, ob_get_contents());
fclose($fp);
ob_end_flush();

exit;
}




function baglan($url){
$oturum = curl_init();
curl_setopt($oturum, CURLOPT_URL, $url);
$h4 = $_SERVER['HTTP_USER_AGENT'];
curl_setopt($oturum, CURLOPT_USERAGENT, $h4);
curl_setopt($oturum, CURLOPT_HEADER, 0);
curl_setopt($oturum, CURLOPT_RETURNTRANSFER, true);
$source=decode_entities(curl_exec($oturum));
curl_close($oturum);
return $source;
}

function dosyaindir($link,$name)
{
	
	$uploads = wp_upload_dir();
$path=$uploads['path'];
$url=$uploads['url'];

$link_info = pathinfo($link);  
$uzanti = strtolower($link_info['extension']); 
$file = $name.'.'.$uzanti;


$curl = curl_init($link);
$fopen = fopen($path.'/'.$file,'w');

$h4 = $_SERVER['HTTP_USER_AGENT'];
curl_setopt($curl, CURLOPT_USERAGENT, $h4);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
curl_setopt($curl, CURLOPT_FILE, $fopen);

$icerik=curl_exec($curl);

curl_close($curl);
fclose($fopen);

$name=$url.'/'.$file;
return $name;
} 

function reklamsil($data){
preg_match_all('|<a class=flash(.*?)</span><br />|',$data,$v);	
for($i=0;$i<count($v[1]);$i++){
$data=str_replace($v[0][$i],'',$data);	
}


$mallas=explode('<script',$data);
for($i=1;$i<count($mallas);$i++){
$mal=$mallas[1];

$malbol=explode('</script>',$mal);	
$datasil='<script'.$malbol[0];
$data=str_replace($datasil,'',$data);	


}

$ex=explode('<a href="http://kavun.mynet.com',$data);
if($ex[0]){$data=$ex[0];}
return strip_tags($data,'<br>');
}

function resimver($ara){
$arat=baglan("http://yasindegirmenci.net/imagesApi.php?q=".urlencode($ara)."&sayfa=0");	
preg_match('|<resim>(.*)</resim>|',$arat,$v);
return $v[1];
}


function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"UTF-8"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
?>