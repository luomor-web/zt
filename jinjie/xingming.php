<?php
/* 姓名字帖 - 生成端（表单见 xingming.html）：姓名笔顺描红 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

// 字段名用 xm，避免与班级姓名行的 name 字段冲突
$name=xx_filter_hanzi($_POST['xm']??'');
if(!$name){
	header("Location: xingming.html");
}else{

// 最多 4 个字
preg_match_all('/./u',$name,$m);
$chars=array_slice($m[0],0,4);

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'1')==='1';

$colors=xx_colors();
$color=$colors[$_POST['zcolor']??'black']??$colors['black'];
$fz=xx_fz_colors();
$fcolor=$fz[($_POST['zcolor']??'black').($_POST['fcolor']??'5')]??$fz['black5'];

$title='姓名字帖（'.$name.'）';
echo xx_sheet_head($title,'',$bglx);
echo '<div><ul>';

$used=0;
foreach($chars as $char){
	// 拼音格行
	if($py){
		$pr=xx_pinyin_row($char);
		echo $pr['html'];
		$used+=$pr['cells'];
		echo xx_page_break($used);
	}
	// 笔顺描红行
	$row=xx_render_hanzi_row($char,$color,$fcolor,1);
	echo $row['html'];
	$used+=$row['cells'];
	echo xx_page_break($used);
	// 空白行
	$r=xx_blank_row(1);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
}

// 姓名连写描红（两遍）
$trace=$name.$name;
for($t=0;$t<2;$t++){
	$r=xx_trace_text_row($trace);
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
