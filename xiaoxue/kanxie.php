<?php
/* 看拼音写词语 - 生成端（表单见 kanxie.html）：拼音格行 + 空白田字格行 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: kanxie.html");
}else{

// 词语来源：组词库全量词语（2-4 字）
$zuci=json_decode(file_get_contents(dirname(__FILE__).'/../data/zuci.json'),true);
$all=[];
foreach($zuci as $words){
	foreach($words as $w){
		$len=mb_strlen($w);
		if($len>=2 && $len<=4){ $all[$w]=true; }
	}
}
$all=array_keys($all);

$num=intval($_POST['num']??'10');
if(!in_array($num,[10,20,30])){ $num=10; }
shuffle($all);
$picked=array_slice($all,0,$num);

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='看拼音写词语';
echo xx_sheet_head($title,'',$bglx);
echo '<div><ul>';

$used=0;
foreach($picked as $w){
	// 拼音格行
	$pr=xx_pinyin_row($w);
	echo $pr['html'];
	$used+=$pr['cells'];
	echo xx_page_break($used);
	// 空白田字格行（每字一格，补齐 12 格）
	preg_match_all('/./u',$w,$m);
	$n=count($m[0]);
	$pad=(12-($n%12))%12;
	for($i=0;$i<$n+$pad;$i++){ echo '<li>&nbsp;</li>'; }
	$used+=$n+$pad;
	echo xx_page_break($used);
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
