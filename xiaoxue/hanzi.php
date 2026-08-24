<?php
/* 汉字练习 - 生成端（表单见 hanzi.html）：按年级学期选择生字，笔顺描红 */
include_once dirname(__FILE__).'/lib.php';

$hz=$_POST['hz']??[];
if(!is_array($hz)){ $hz=[$hz]; }
$words=xx_filter_hanzi(implode('',$hz));
if(!$words){
	header("Location: hanzi.html");
}else{

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'0')==='1';
$bs=($_POST['bs']??'0')==='1';

$colors=xx_colors();
$color=$colors[$_POST['zcolor']??'black']??$colors['black'];
$fz=xx_fz_colors();
$fcolor=$fz[($_POST['zcolor']??'black').($_POST['fcolor']??'5')]??$fz['black5'];

/* 学期名称（仅用于页头标题） */
$term=$_POST['term']??'';
$term_names=['1上'=>'一年级上','1下'=>'一年级下','2上'=>'二年级上','2下'=>'二年级下','3上'=>'三年级上','3下'=>'三年级下','4上'=>'四年级上','4下'=>'四年级下','5上'=>'五年级上','5下'=>'五年级下','6上'=>'六年级上','6下'=>'六年级下'];

$title='汉字练习'.(isset($term_names[$term])?'（'.$term_names[$term].'学期）':'');
echo xx_sheet_head($title,'',$bglx);
echo '<div><ul>';

preg_match_all('/./u',$words,$chars);
$used=0;
foreach($chars[0] as $char){
	// 拼音格行（可选）
	if($py){
		$pr=xx_pinyin_row($char);
		echo $pr['html'];
		$used+=$pr['cells'];
		echo xx_page_break($used);
	}
	// 笔顺描红行
	$row=xx_render_hanzi_row($char,$color,$fcolor,$bs);
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
