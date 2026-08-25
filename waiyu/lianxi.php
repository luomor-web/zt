<?php
/* 外语练习 - 生成端（表单见 lianxi.html）：字母 / 单词 / 句子 / 默写 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: lianxi.html");
}else{

$langs=['ko'=>'韩语','ja'=>'日语','vi'=>'越南语','ru'=>'俄语','es'=>'西班牙语','fr'=>'法语','de'=>'德语','en'=>'英语'];
$lang=$_POST['lang']??'';
if(!isset($langs[$lang])){ header("Location: lianxi.html"); }
else{

$mode=$_POST['mode']??'letter';
if(!in_array($mode,['letter','word','sentence','write'])){ $mode='letter'; }
$num=intval($_POST['num']??'10');
$blank=intval($_POST['blank']??'2');
if(!in_array($blank,[1,2,3])){ $blank=2; }
$blank_html=str_repeat('<div class="eng-grid eng-blank"></div>',$blank);

$data=json_decode(file_get_contents(dirname(__FILE__).'/../data/guoji/'.$lang.'.json'),true);

$mode_names=['letter'=>'字母','word'=>'单词','sentence'=>'句子','write'=>'默写'];
$title=$langs[$lang].'练习（'.$mode_names[$mode].'）';

$css=xx_eng_css()
	.'.eng-wrap{width:938px;margin:0 auto;}'
	.'.eng-item{margin-bottom:18px;}'
	.'.eng-word-head{display:flex;justify-content:space-between;align-items:baseline;padding:0 4px 4px;}'
	.'.eng-word-zh{font-size:18px;color:#666;}';
echo xx_sheet_head($title,$css,'tzg');
echo '<div class="eng-wrap">';

if($mode==='letter'){
	foreach($data['letters'] as $letter){
		$text=$letter.' '.$letter.' '.$letter.' '.$letter;
		echo '<div class="eng-item">';
		echo '<div class="eng-grid"><div class="eng-text">'.htmlspecialchars($text,ENT_QUOTES,'UTF-8').'</div></div>';
		echo $blank_html;
		echo '</div>';
	}
}elseif($mode==='word' || $mode==='write'){
	$words=$data['words'];
	shuffle($words);
	$words=array_slice($words,0,$num);
	foreach($words as $w){
		$ww=htmlspecialchars($w['w'],ENT_QUOTES,'UTF-8');
		$zh=htmlspecialchars($w['zh'],ENT_QUOTES,'UTF-8');
		echo '<div class="eng-item">';
		echo '<div class="eng-word-head"><span class="eng-word-zh">'.$zh.'</span></div>';
		if($mode==='word'){
			echo '<div class="eng-grid"><div class="eng-text">'.$ww.' '.$ww.' '.$ww.'</div></div>';
		}
		echo $blank_html;
		echo '</div>';
	}
}else{
	$sentences=$data['sentences'];
	shuffle($sentences);
	$sentences=array_slice($sentences,0,$num);
	foreach($sentences as $s){
		$t=htmlspecialchars($s['t'],ENT_QUOTES,'UTF-8');
		$zh=htmlspecialchars($s['zh'],ENT_QUOTES,'UTF-8');
		echo '<div class="eng-item">';
		echo '<div class="eng-grid"><div class="eng-text">'.$t.'</div></div>';
		echo '<div class="eng-zh">'.$zh.'</div>';
		echo $blank_html;
		echo '</div>';
	}
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
}
