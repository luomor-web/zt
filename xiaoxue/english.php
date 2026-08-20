<?php
/* 英语练习 - 生成端（表单见 english.html）：字母 / 单词 / 句子 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: english.html");
}else{

$mode=$_POST['mode']??'letter';
if(!in_array($mode,['letter','word','sentence'])){ $mode='letter'; }
$grade=intval($_POST['grade']??'0');//0=全部年级
$num=intval($_POST['num']??'10');
$blank=intval($_POST['blank']??'2');//空白练习行数
if(!in_array($blank,[1,2,3])){ $blank=2; }
$blank_html=str_repeat('<div class="eng-grid eng-blank"></div>',$blank);

$mode_names=['letter'=>'字母','word'=>'单词','sentence'=>'句子'];
$title='英语练习（'.$mode_names[$mode].'）';

$css=xx_eng_css()
	.'.eng-wrap{width:938px;margin:0 auto;}'
	.'.eng-item{margin-bottom:28px;}'
	.'.eng-word-head{display:flex;justify-content:space-between;align-items:baseline;padding:0 4px 4px;}'
	.'.eng-word-zh{font-size:18px;color:#666;}';
echo xx_sheet_head($title,$css,'tzg');
echo '<div class="eng-wrap">';

if($mode==='letter'){
	// 字母：Aa-Zz，每个字母大小写一行描红 + 一行空白
	for($i=0;$i<26;$i++){
		$upper=chr(65+$i);
		$lower=chr(97+$i);
		$text=$upper.$lower.' '.$upper.$lower.' '.$upper.$lower.' '.$upper.$lower;
		echo '<div class="eng-item">';
		echo '<div class="eng-grid"><div class="eng-text">'.$text.'</div></div>';
		echo $blank_html;
		echo '</div>';
	}
}elseif($mode==='word'){
	$words=json_decode(file_get_contents(dirname(__FILE__).'/../data/english_words.json'),true);
	if($grade>=1 && $grade<=6){
		$words=array_values(array_filter($words,function($w)use($grade){ return intval($w['grade'])<=$grade; }));
	}
	shuffle($words);
	$words=array_slice($words,0,$num);
	foreach($words as $w){
		$en=htmlspecialchars($w['en'],ENT_QUOTES,'UTF-8');
		$zh=htmlspecialchars($w['zh'],ENT_QUOTES,'UTF-8');
		echo '<div class="eng-item">';
		echo '<div class="eng-word-head"><span class="eng-word-zh">'.$zh.'</span></div>';
		echo '<div class="eng-grid"><div class="eng-text">'.$en.' '.$en.' '.$en.'</div></div>';
		echo $blank_html;
		echo '</div>';
	}
}else{
	$sentences=json_decode(file_get_contents(dirname(__FILE__).'/../data/english_sentences.json'),true);
	shuffle($sentences);
	$sentences=array_slice($sentences,0,$num);
	foreach($sentences as $s){
		$en=htmlspecialchars($s['en'],ENT_QUOTES,'UTF-8');
		$zh=htmlspecialchars($s['zh'],ENT_QUOTES,'UTF-8');
		echo '<div class="eng-item">';
		echo '<div class="eng-grid"><div class="eng-text">'.$en.'</div></div>';
		echo '<div class="eng-zh">'.$zh.'</div>';
		echo $blank_html;
		echo '</div>';
	}
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
