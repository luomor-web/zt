<?php
/* 趣味主题字帖 - 生成端（表单见 quwei.html）：动物图片作田字格背景 */
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
$css='li.pic{position:relative;}'
	.'li.pic img{position:absolute;left:6px;top:6px;width:68px;height:68px;object-fit:contain;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

/* 输出一个图片格 */
function pic_cell($animal){
	if($animal==='random'){ $animal=sprintf('%03d',mt_rand(1,70)); }
	return '<li class="pic"><img src="img/animals/'.$animal.'.png" alt=""></li>';
}

preg_match_all('/./u',$words,$chars);
$used=0;
foreach($chars[0] as $char){
	// 拼音格行（可选，首格放动物图片）
	if($py){
		echo pic_cell($animal);
		echo '<li class="py">'.htmlspecialchars(Pinyin::getPinyin($char),ENT_QUOTES,'UTF-8').'</li>';
		for($i=2;$i<12;$i++){ echo '<li class="py">&nbsp;</li>'; }
		$used+=12;
		echo xx_page_break($used);
	}
	// 描红行（首格放动物图片，后面接文字格和空格）
	echo pic_cell($animal);
	$r=xx_trace_text_row($char);
	$cells=explode('<li',$r['html']);
	array_shift($cells);//去掉第一个空段，保留文字格和空格
	array_pop($cells);//图片格占 1 格，去掉最后一个空格保持 12 格
	echo '<li'.implode('<li',$cells);
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
