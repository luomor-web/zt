<?php
/* 趣味主题字帖 - 生成端（表单见 quwei.html）：动物田字格图直接渲染汉字 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

$words=xx_filter_hanzi($_POST['words']??'');
if(!$words){
	header("Location: quwei.html");
}else{

$animal=$_POST['animal']??'random';//动物编号 001-070 或 random
if($animal!=='random' && !preg_match('/^0[0-7][0-9]$/',$animal)){ $animal='random'; }

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'1')==='1';

$title='趣味主题字帖';
$css='li.picchar{position:relative;background-size:cover !important;background-position:center !important;top: 20px;}'
	.'li.picchar span{position:absolute;left:0;bottom:6px;width:100%;text-align:center;font-size:34px;line-height:1;color:#c8c8c8;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

/* 输出动物编号（random 时随机一只） */
function pick_animal($animal){
	if($animal==='random'){ $animal=sprintf('%03d',mt_rand(1,70)); }
	return $animal;
}
/* 输出一个图片格（仅动物图，无文字） */
function pic_cell($a){
	return '<li class="picchar" style="background:url(img/animals/'.$a.'.png);"><span>&nbsp;</span></li>';
}
/* 输出一个带汉字的动物田字格格 */
function pic_char_cell($a,$char){
	return '<li class="picchar" style="background:url(img/animals/'.$a.'.png);"><span>'.$char.'</span></li>';
}

preg_match_all('/./u',$words,$chars);
$used=0;
foreach($chars[0] as $char){
	$a=pick_animal($animal);
	// 拼音格行（可选，首格放动物图片）
	if($py){
		echo pic_cell($a);
		echo '<li class="py">'.htmlspecialchars(Pinyin::getPinyin($char),ENT_QUOTES,'UTF-8').'</li>';
		for($i=2;$i<12;$i++){ echo '<li class="py">&nbsp;</li>'; }
		$used+=12;
		echo xx_page_break($used);
	}
	// 描红行：首格为动物田字格图直接渲染汉字，其后 3 个普通描红格 + 8 空格，共 12 格
	echo pic_char_cell($a,$char);
	for($i=0;$i<3;$i++){ echo '<li style="color:#c8c8c8">'.$char.'</li>'; }
	for($i=0;$i<8;$i++){ echo '<li>&nbsp;</li>'; }
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
