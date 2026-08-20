<?php
/* 组词字帖练习 - 生成端（表单见 zuci.html） */
include_once dirname(__FILE__).'/lib.php';

$words=xx_filter_hanzi($_POST['words']??'');
if(!$words){
	header("Location: zuci.html");
}else{

$zcnum=intval($_POST['zcnum']??'2');//每字组词数
if(!in_array($zcnum,[1,2,3])){ $zcnum=2; }
$bglx=$_POST['types']??'tzg';//格子类型
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'0')==='1';//拼音开关
$bs=($_POST['bs']??'0')==='1';//笔顺填充

$colors=xx_colors();
$color=$colors[$_POST['zcolor']??'black']??$colors['black'];
$fz=xx_fz_colors();
$fcolor=$fz[($_POST['zcolor']??'black').($_POST['fcolor']??'5')]??$fz['black5'];

$zuci=json_decode(file_get_contents(dirname(__FILE__).'/../data/zuci.json'),true);

$title='组词字帖练习';
echo xx_sheet_head($title,'.zc-label{font-size:22px;color:#666;padding:10px 0 0 4px;font-family:'.xx_kaiti().';}',$bglx);
echo '<div><ul>';

preg_match_all('/./u',$words,$hz);
$used=0;
foreach($hz[0] as $char){
	// 拼音格行（独占一行）
	if($py){
		$pr=xx_pinyin_row($char);
		echo $pr['html'];
		$used+=$pr['cells'];
		echo xx_page_break($used);
	}

	// 生字笔顺描红行
	$row=xx_render_hanzi_row($char,$color,$fcolor,$bs);
	echo $row['html'];
	$used+=$row['cells'];
	echo xx_page_break($used);

	// 组词描红行（每词先拼音格行再描红行）
	if(isset($zuci[$char])){
		$words_list=array_slice($zuci[$char],0,$zcnum);
		foreach($words_list as $w){
			if($py){
				$pr=xx_pinyin_row($w);
				echo $pr['html'];
				$used+=$pr['cells'];
				echo xx_page_break($used);
			}
			$r=xx_trace_text_row($w);
			echo $r['html'];
			$used+=$r['cells'];
			echo xx_page_break($used);
		}
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
