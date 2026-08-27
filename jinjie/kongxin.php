<?php
/* 空心字帖 - 生成端（表单见 kongxin.html）：大号空心字描红 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

$words=xx_filter_hanzi($_POST['words']??'');
if(!$words){
	header("Location: kongxin.html");
}else{

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

// 空心字颜色
$stroke_colors=['green'=>'#00b050','black'=>'#555555','red'=>'#980f29'];
$sc=$stroke_colors[$_POST['zcolor']??'black']??$stroke_colors['black'];

$title='空心字帖';
$css='li.kx{color:transparent;-webkit-text-stroke:1.5px '.$sc.';text-stroke:1.5px '.$sc.';}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

preg_match_all('/./u',$words,$chars);
$used=0;
foreach($chars[0] as $char){
	// 空心字描红行（3 个空心 + 补齐空格）
	for($i=0;$i<3;$i++){ echo '<li class="kx">'.$char.'</li>'; }
	for($i=3;$i<12;$i++){ echo '<li>&nbsp;</li>'; }
	$used+=12;
	echo xx_page_break($used);
	// 空白行
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
