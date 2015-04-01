<?php
header('Content-Type: text/html; charset=utf-8');
require("pages/ayarlar.php");
$uyeBilgi=sureklikontrol($adaminkeyi);
$wordpressayarlar=scriptver("hepsi");
$yapiayar=base64_decode($wordpressayarlar["wordpressayarlar"]);
$yapiayar=json_decode($yapiayar,true);
$videoozel=$yapiayar['videoozel'];
$dakikaozel=$yapiayar['dakikaozel'];
$resimozel=$yapiayar['resimozel'];
if($wordpressayarlar["scriptName"]!="wordpress"){exit("site wp değil");}
if(isset($_GET['sayfa'])){
$basla=$_GET['sayfa'];	
}else{
$basla=0;
}


if(file_exists("../wp-load.php")){require("../wp-load.php");}else{exit("wp degil");}
if(isset($_GET['goster'])){

if(isset($_GET['anasiniguncelle']) and isset($_POST['videoID'])){
global $wpdb;
$pref=$wpdb->prefix;
	$videoID=mysql_real_escape_string(stripslashes($_POST['videoID']));
	$resimurl=mysql_real_escape_string(stripslashes($_POST['resimurl']));
	$videourl=mysql_real_escape_string(stripslashes($_POST['videourl']));
	$videoaciklama=mysql_real_escape_string(stripslashes($_POST['videoaciklama']));
	$videosure=mysql_real_escape_string(stripslashes($_POST['videosure']));
	update_post_meta($videoID,$videoozel,$videourl);
	update_post_meta($videoID,$resimozel,$resimurl);
	update_post_meta($videoID,$dakikaozel,$videosure);
	delete_post_meta($videoID, 'bozuk', 'evet');
	mysql_query("update ".$pref."posts set post_content='$videoaciklama' where ID='$videoID' limit 1");
	mysql_query("delete from ".$pref."postmeta where meta_key='bozuk' and meta_value='evet' and post_id='$videoID'  limit 1");
echo 'Güncellendi';exit;

	exit;
}	
	

if(isset($_GET['bosalt'])){mysql_query("truncate rigo_bozuk_videolar");}
	echo '<a href="'.$_SERVER['PHP_SELF'].'?goster=true&bosalt=true">Boşalt</a><br>';
if(is_numeric($_GET['sil'])){
 mysql_query("delete from rigo_bozuk_videolar where bozuk_id='".mysql_real_escape_string($_GET['sil'])."' limit 1");	
}
	echo '<style type="text/css">
	table {
    border-collapse: collapse;
    border-spacing: 0;
    border: 1px solid #bbb;
}
td,th {
    border-top: 1px solid #ddd;
    padding: 4px 8px;
}
tbody tr:nth-child(even)  td { background-color: #eee; }

media screen and (max-width: 640px) {
	table {
		overflow-x: auto;
		display: block;
	}
}
	</style>
	
	
<table width="100%">
  <tr>
    <td width="25%">Wp Admin Url</td>
    <td width="25%">Video Url</td>
    <td width="25%">Embed Url</td>
    <td width="25%">Sil</td>
  </tr>
</table>

';
	$sor=mysql_query("select * from rigo_bozuk_videolar order by bozuk_id asc");
	if(mysql_num_rows($sor)>0){
		while($cek=mysql_fetch_array($sor)){
		$bozukID=$cek['bozuk_id'];
		$bozuk_video_id=trim($cek['bozuk_video_id']);
		$bozuk_video_url=$cek['bozuk_video_url'];
		$bozuk_video_kod=$cek['bozuk_video_kod'];
		$postamk = get_post($bozuk_video_id);
		$post_content=$postamk->post_content;
		$video_ozel_alan=get_post_meta($bozuk_video_id,$videoozel,true);
		$resim_ozel_alan=get_post_meta($bozuk_video_id,$resimozel,true);
		$sure=get_post_meta($bozuk_video_id,$dakikaozel,true);
		echo '
<table width="100%">
  <tr>
    <td width="25%"><a target="_blank" href="/wp-admin/post.php?post='.$cek['bozuk_video_id'].'&action=edit">Düzenle</a></td>
    <td width="25%">'.$cek['bozuk_video_url'].'</td>
    <td width="25%"><a href="'.$cek['bozuk_video_kod'].'">İzle</a></td>
    <td width="25%"><a href="'.$_SERVER['PHP_SELF'].'?goster=true&sil='.$cek['bozuk_id'].'">Sil</a></td>
  </tr>
</table>
<form method="post" action="'.$_SERVER['PHP_SELF'].'?goster=true&anasiniguncelle=true">
<table width="100%">
  <tr>
    <td width="25%">Yeni Resim Url</td>
    <td width="25%">Yeni Video Özel Alan</td>
    <td width="25%">Video Açıklama</td>
    <td width="25%">Yeni Süre</td>
  </tr>
</table>
<input type="hidden" name="videoID" value="'.$cek['bozuk_video_id'].'">
<table width="100%">
  <tr>
    <td width="25%"><textarea style="width:100%; height:150px;" type="text" name="resimurl">'.$resim_ozel_alan.'</textarea></td>
    <td width="25%"><textarea style="width:100%; height:150px;" type="text" name="videourl">'.$video_ozel_alan.'</textarea></td>
    <td width="25%"><textarea style="width:100%; height:150px;" type="text" name="videoaciklama">'.$post_content.'</textarea></td>
    <td width="25%"><textarea style="width:100%; height:150px;" type="text" name="videosure">'.$sure.'</textarea></td>
  </tr>
</table>
<input type="submit" formtarget="_blank" value="Kaydet" align="center" name="guncelle">
</form>
';

		}
			
	}
	
exit;	
}
if(isset($_GET['kur'])){
	mysql_query("
CREATE TABLE IF NOT EXISTS `rigo_bozuk_videolar` (
  `bozuk_id` int(11) NOT NULL AUTO_INCREMENT,
  `bozuk_video_id` int(11) NOT NULL DEFAULT '0',
  `bozuk_video_url` text NOT NULL,
  `bozuk_video_kod` text NOT NULL,
  PRIMARY KEY (`bozuk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
echo "Kurulum Tamamdır";
exit;	
}
global $wpdb;
$pref=$wpdb->prefix;
$args = array(
	'posts_per_page'   => 1,
	'offset'           => $basla,
	'orderby'          => 'post_date',
	'order'            => 'ASC',
	'post_type'        => 'post',
	'post_status'      => 'publish'
	);
$girdimi=false;
$myposts = get_posts( $args );
if(!isset($myposts[0])){exit("bitti");}
foreach ( $myposts as $post ) : setup_postdata( $post ); 
$ID=$post->ID;
echo $post->post_title." Videosu inceleniyor<br>";
$girdimi=true;
if(empty($post->post_title)){exit("duzenledim");}
$post_content=$post->post_content;
$videoget=get_post_meta($ID,$videoozel,true);
preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $videoget, $matches);
if(isset($matches[1]) and eregi($botyapi,$matches[1])){
echo $matches[1].'<br>';
	$hatasorgula=json_decode(baglank("http://adminiabilisim.com/wp/a.php?link=".trim($matches[1])));
	if($hatasorgula->hata==true){
	$bozukvideoUrl=get_permalink($ID);
	$bozuk_video_code=$matches[1];
		$sor=mysql_num_rows(mysql_query("select * from rigo_bozuk_videolar where bozuk_video_id='$ID'"));
		if($sor<1){
			add_post_meta($ID,"bozuk","evet");
		mysql_query("insert into rigo_bozuk_videolar(bozuk_video_id,bozuk_video_url,bozuk_video_kod)values('$ID','$bozukvideoUrl','$bozuk_video_code')");
		echo "Eklendi";
		}else{
		echo "Daha Önce Eklenmiş";	
		}
	}
}

preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $post_content, $matches);
if(isset($matches[1]) and eregi($botyapi,$matches[1])){
echo $matches[1].'<br>';
	$hatasorgula=json_decode(baglank("http://adminiabilisim.com/wp/a.php?link=".trim($matches[1])));
	if($hatasorgula->hata==true){
	$bozukvideoUrl=get_permalink($ID);
	$bozuk_video_code=$matches[1];
		$sor=mysql_num_rows(mysql_query("select * from rigo_bozuk_videolar where bozuk_video_id='$ID'"));
		if($sor<1){
			add_post_meta($ID,"bozuk","evet");
		mysql_query("insert into rigo_bozuk_videolar(bozuk_video_id,bozuk_video_url,bozuk_video_kod)values('$ID','$bozukvideoUrl','$bozuk_video_code')");
		echo "Eklendi";
		}else{
		echo "Daha Önce Eklenmiş";	
		}
	}
}
endforeach; 
wp_reset_postdata();
?>
<meta http-equiv="refresh" content="2;url=<?php echo $_SERVER['PHP_SELF'];?>?sayfa=<?php echo ($basla+1);?>&kategoriID=<?php echo $kategoriID;?>">

 <?php 
function baglank($url){
$oturum = curl_init();
curl_setopt($oturum, CURLOPT_URL, $url);
$h4 = $_SERVER['HTTP_USER_AGENT'];
curl_setopt($oturum, CURLOPT_USERAGENT, "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0");
curl_setopt($oturum, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
curl_setopt($oturum, CURLOPT_HEADER, 0);
curl_setopt($oturum, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($oturum, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($oturum, CURLOPT_TIMEOUT, 10);
$source=curl_exec($oturum);
curl_close($oturum);
return $source;
}

?>