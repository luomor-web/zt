<?php
/* 小学必备古诗练习 - 生成端（表单见 gushi.html，诗单数据 data/gushi.json） */
include_once dirname(__FILE__).'/lib.php';

$gushi=json_decode(file_get_contents(dirname(__FILE__).'/../data/gushi.json'),true);

$sel=$_POST['poems']??[];
if(!is_array($sel)){ $sel=[$sel]; }
$sel=array_values(array_filter(array_map('intval',$sel),function($i)use($gushi){ return isset($gushi[$i]); }));
if(!$sel){
	header("Location: gushi.html");
}else{

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'0')==='1';//拼音
$mx=($_POST['mx']??'1')==='1';//默写空白行
$trace_rows=intval($_POST['tr']??'1');//每句描红行数
if(!in_array($trace_rows,[1,2])){ $trace_rows=1; }

$title='古诗练习';
$css='.gs-title{font-size:34px;text-align:center;font-weight:bold;padding:26px 0 6px;font-family:'.xx_kaiti().';}'
	.'.gs-author{font-size:20px;text-align:center;color:#666;padding-bottom:18px;font-family:'.xx_kaiti().';}'
	.'.gs-py{font-size:15px;color:#999;text-align:center;font-family:sans-serif;line-height:1;padding-top:4px;}';
echo xx_sheet_head($title,$css,$bglx);

foreach($sel as $pi=>$idx){
	$poem=$gushi[$idx];
	if($pi>0){ echo "<div class='afterpage'>"; } else { echo '<div>'; }
	echo '<div class="gs-title">'.htmlspecialchars($poem['title'],ENT_QUOTES,'UTF-8').'</div>';
	echo '<div class="gs-author">〔'.htmlspecialchars($poem['dynasty'],ENT_QUOTES,'UTF-8').'〕 '.htmlspecialchars($poem['author'],ENT_QUOTES,'UTF-8').'</div>';
	echo '<ul>';

	$used=0;
	foreach($poem['content'] as $line){
		// 去掉标点用于格子显示（标点不练字）
		$plain=xx_filter_hanzi($line);
		// 拼音格行（独占一行，与下方字格一一对应）
		if($py){
			$pr=xx_pinyin_row($plain);
			echo $pr['html'];
			$used+=$pr['cells'];
			echo xx_page_break($used);
		}
		// 描红行
		for($t=0;$t<$trace_rows;$t++){
			$r=xx_trace_text_row($plain);
			echo $r['html'];
			$used+=$r['cells'];
			echo xx_page_break($used);
		}
		// 默写空白行
		if($mx){
			$r=xx_blank_row(1);
			echo $r['html'];
			$used+=$r['cells'];
			echo xx_page_break($used);
		}
	}
	echo '</ul></div>';
}

echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
