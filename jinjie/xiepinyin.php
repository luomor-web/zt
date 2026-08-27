<?php
/* 看汉字写拼音 - 生成端（表单见 xiepinyin.html）：汉字描红 + 空白拼音格 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

$words=xx_filter_hanzi($_POST['words']??'');
if(!$words){
	header("Location: xiepinyin.html");
}else{

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='看汉字写拼音';
echo xx_sheet_head($title,'',$bglx);
echo '<div><ul>';

preg_match_all('/./u',$words,$chars);
$used=0;
foreach($chars[0] as $char){
	// 汉字描红行
	$r=xx_trace_text_row($char);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
	// 空白拼音格行（供写拼音）
	for($i=0;$i<12;$i++){ echo '<li class="py">&nbsp;</li>'; }
	$used+=12;
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
