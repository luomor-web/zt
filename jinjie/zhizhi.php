<?php
/* 空白作业纸 - 生成端（表单见 zhizhi.html）：6 种纸型 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: zhizhi.html");
}else{

$type=$_POST['ptype']??'tzg';
if(!in_array($type,['tzg','mzg','fangge','sxsg','zuowen','hengxian'])){ $type='tzg'; }
$pages=intval($_POST['pages']??'1');
if(!in_array($pages,[1,2,3])){ $pages=1; }

$type_names=['tzg'=>'田字格','mzg'=>'米字格','fangge'=>'方格','sxsg'=>'四线三格','zuowen'=>'作文格','hengxian'=>'横线'];
$title='空白作业纸（'.$type_names[$type].'）';

$bglx='tzg';
if($type==='mzg'){ $bglx='mzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if(in_array($type,['tzg','mzg']) && $bgcolor!=='black'){ $bglx.=$bgcolor; }

$extra='';
switch($type){
case 'fangge':
	$extra='li{border:1px solid #999;background:none;}';
	break;
case 'zuowen':
	$extra='li{width:46px;height:46px;line-height:48px;font-size:30px;border:1px solid #999;background:none;margin:2px 0 2px -1px;}';
	break;
case 'hengxian':
	$extra='li{border-bottom:1px solid #999;background:none;height:56px;}';
	break;
}
if(in_array($type,['fangge','zuowen','hengxian'])){ $bglx='tzg'; }

$css=xx_eng_css().$extra;
echo xx_sheet_head($title,$css,$bglx);

// 每页 15 行
for($p=0;$p<$pages;$p++){
	if($p>0){ echo "<div class='afterpage'>"; } else { echo '<div>'; }
	echo '<ul>';
	switch($type){
	case 'tzg':
	case 'mzg':
	case 'fangge':
		for($i=0;$i<15*12;$i++){ echo '<li>&nbsp;</li>'; }
		break;
	case 'zuowen':
		for($i=0;$i<15*20;$i++){ echo '<li>&nbsp;</li>'; }
		break;
	case 'sxsg':
		for($i=0;$i<15;$i++){ echo '<div class="eng-grid" style="margin-bottom:0;border-bottom:none;"></div>'; }
		// 四线三格行高 96px，15 行铺满一页
		break;
	case 'hengxian':
		for($i=0;$i<20;$i++){ echo '<li style="width:100%;">&nbsp;</li>'; }
		break;
	}
	echo '</ul></div>';
}

echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
