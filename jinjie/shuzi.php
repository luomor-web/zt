<?php
/* 数字字帖 - 生成端（表单见 shuzi.html）：0-9 田字格描红 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: shuzi.html");
}else{

$mode=$_POST['mode']??'order';
if(!in_array($mode,['order','random'])){ $mode='order'; }
$num=intval($_POST['num']??'10');
if(!in_array($num,[10,20])){ $num=10; }
$blank=intval($_POST['blank']??'2');
if(!in_array($blank,[1,2,3])){ $blank=2; }

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='数字字帖（'.($mode==='order'?'0-9 顺序':'随机').'）';
// 方格样式
$css='li{border:1px solid #999;background:none;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

if($mode==='order'){
	$digits=range(0,9);
}else{
	$digits=[];
	for($i=0;$i<$num;$i++){ $digits[]=mt_rand(0,9); }
}

$used=0;
foreach($digits as $d){
	// 田字格描红行：数字重复 6 个
	$r=xx_trace_text_row(str_repeat($d,6));
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
	// 空白行
	for($b=0;$b<$blank;$b++){
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
