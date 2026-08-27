<?php
/* 打卡表 - 生成端（表单见 daka.html）：30 天习惯打卡 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: daka.html");
}else{

$item=trim($_POST['item']??'');
if($item===''){ $item='阅读'; }
$item=htmlspecialchars($item,ENT_QUOTES,'UTF-8');
$days=intval($_POST['days']??'30');
if(!in_array($days,[21,30])){ $days=30; }

$title=$item.'打卡表';

$css='.dk-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.dk-title{font-size:26px;font-weight:bold;text-align:center;padding:16px 0;}'
	.'.dk-table{width:100%;border-collapse:collapse;}'
	.'.dk-table th{border:1px solid #999;background:#f0f0f0;padding:8px 4px;font-size:14px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.dk-table td{border:1px solid #999;height:44px;}';
echo xx_sheet_head($title,$css);
echo '<div class="dk-wrap">';
echo '<div class="dk-title">'.$item.'打卡表（'.$days.' 天）</div>';
echo '<table class="dk-table">';

// 表头：日期 1-15 / 16-30 两行布局（项目 | 1..15）
$half=ceil($days/2);
for($part=0;$part<2;$part++){
	$start=$part*$half+1;
	$end=min($days,($part+1)*$half);
	if($start>$end) break;
	echo '<tr><th>项目</th>';
	for($d=$start;$d<=$end;$d++){ echo '<th>'.$d.'日</th>'; }
	echo '</tr>';
	echo '<tr><td style="text-align:center;font-size:15px;">'.$item.'</td>';
	for($d=$start;$d<=$end;$d++){ echo '<td></td>'; }
	echo '</tr>';
}

echo '</table></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
