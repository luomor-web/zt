<?php
/* 语文课贴 - 生成端（表单见 ketie.html）：按年级学期生成整册生字字帖 */
include_once dirname(__FILE__).'/lib.php';

$term=$_POST['term']??'';
$hanzi=json_decode(file_get_contents(dirname(__FILE__).'/../data/hanzi.json'),true);
if(!isset($hanzi[$term])){
	header("Location: ketie.html");
}else{

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'1')==='1';

$colors=xx_colors();
$color=$colors[$_POST['zcolor']??'black']??$colors['black'];
$fz=xx_fz_colors();
$fcolor=$fz[($_POST['zcolor']??'black').($_POST['fcolor']??'5')]??$fz['black5'];

$term_names=['1上'=>'一年级上学期','1下'=>'一年级下学期','2上'=>'二年级上学期','2下'=>'二年级下学期',
'3上'=>'三年级上学期','3下'=>'三年级下学期','4上'=>'四年级上学期','4下'=>'四年级下学期',
'5上'=>'五年级上学期','5下'=>'五年级下学期','6上'=>'六年级上学期','6下'=>'六年级下学期'];

$chars=$hanzi[$term];
$title='语文课贴（'.$term_names[$term].'）';
echo xx_sheet_head($title,'',$bglx);
echo '<div><ul>';

$used=0;
foreach($chars as $char){
	// 拼音格行（可选）
	if($py){
		$pr=xx_pinyin_row($char);
		echo $pr['html'];
		$used+=$pr['cells'];
		echo xx_page_break($used);
	}
	// 笔顺描红行
	$row=xx_render_hanzi_row($char,$color,$fcolor,0);
	echo $row['html'];
	$used+=$row['cells'];
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
