<?php
/* 歇后语练习 - 生成端（表单见 xiehouyu.html）：描红 / 填空两种模式 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: xiehouyu.html");
}else{

$data=json_decode(file_get_contents(dirname(__FILE__).'/../data/xiehouyu.json'),true);
shuffle($data);

$num=intval($_POST['num']??'10');
if(!in_array($num,[10,20,30])){ $num=10; }
$data=array_slice($data,0,$num);

$mode=$_POST['mode']??'trace';
if(!in_array($mode,['trace','fill'])){ $mode='trace'; }

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='歇后语练习（'.($mode==='trace'?'描红':'填空').'）';
$css='.xh-q{width:100%;background:none;height:auto;text-align:left;font-size:18px;color:#555;line-height:2;padding:4px 4px 8px;font-family:'.xx_kaiti().';}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

$used=0;
foreach($data as $item){
	// 前半句（文字行）
	echo '<li class="xh-q">'.htmlspecialchars($item['q'],ENT_QUOTES,'UTF-8').' ——</li>';
	if($mode==='trace'){
		// 后半句描红
		$r=xx_trace_text_row(xx_filter_hanzi($item['a']));
		echo $r['html'];
		$used+=$r['cells'];
		echo xx_page_break($used);
	}
	// 空白行（描红模式供抄写，填空模式供填写答案）
	$r=xx_blank_row(1);
	echo $r['html'];
	$used+=$r['cells'];
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
