<?php
/* 成语练习 - 生成端（表单见 chengyu.html）：成语描红 + 拼音 + 释义 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: chengyu.html");
}else{

$data=json_decode(file_get_contents(dirname(__FILE__).'/../data/chengyu.json'),true);
shuffle($data);

$num=intval($_POST['num']??'10');
if(!in_array($num,[10,20,30])){ $num=10; }
$data=array_slice($data,0,$num);

$py=($_POST['py']??'1')==='1';//拼音
$sy=($_POST['sy']??'1')==='1';//释义
$mx=($_POST['mx']??'1')==='1';//默写空白行

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='成语练习';
$css='.cy-meaning{width:100%;background:none;height:auto;text-align:left;font-size:16px;color:#888;line-height:1.8;padding:2px 4px 10px;font-family:sans-serif;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

$used=0;
foreach($data as $item){
	$word=$item['word'];
	// 拼音格行
	if($py){
		$pys=[];
		preg_match_all('/./u',$word,$m);
		foreach($m[0] as $c){ $pys[]=Pinyin::getPinyin($c); }
		$n=count($pys);
		$pad=(12-($n%12))%12;
		for($i=0;$i<$n;$i++){ echo '<li class="py">'.htmlspecialchars($pys[$i],ENT_QUOTES,'UTF-8').'</li>'; }
		for($i=0;$i<$pad;$i++){ echo '<li class="py">&nbsp;</li>'; }
		$used+=$n+$pad;
		echo xx_page_break($used);
	}
	// 成语描红行
	$r=xx_trace_text_row($word);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
	// 释义行
	if($sy){
		echo '<li class="cy-meaning">【释义】'.htmlspecialchars($item['meaning'],ENT_QUOTES,'UTF-8').'</li>';
	}
	// 默写空白行
	if($mx){
		$r=xx_blank_row(1);
		echo $r['html'];
		$used+=$r['cells'];
		echo xx_page_break($used);
	}
}

// 堆满整页
$rows=ceil($used/12);
$total_pages=max(1,ceil($rows/15));
$rest=$total_pages*15*12-$used;
for($i=0;$i<$rest;$i++){ echo '<li>&nbsp;</li>'; }

echo '</ul></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
