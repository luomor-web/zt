<?php
/* 课程表 - 生成端（表单见 kechengbiao.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: kechengbiao.html");
}else{

$title='课程表';

$css='.kc-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.kc-table{width:100%;border-collapse:collapse;}'
	.'.kc-table th,.kc-table td{border:2px solid #999;text-align:center;}'
	.'.kc-table th{background:#f0f0f0;padding:14px 8px;font-size:20px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.kc-table td{height:86px;font-size:16px;}'
	.'.kc-table td.time{width:110px;background:#f7f7f7;font-size:15px;color:#666;-webkit-print-color-adjust:exact;print-color-adjust:exact;}';
echo xx_sheet_head($title,$css);
echo '<div class="kc-wrap"><table class="kc-table">';

$days=['星期一','星期二','星期三','星期四','星期五'];
echo '<tr><th colspan="2">时间</th>';
foreach($days as $d){ echo '<th>'.$d.'</th>'; }
echo '</tr>';

$slots=[
	['上午','第 1 节'],['','第 2 节'],['','第 3 节'],['','第 4 节'],
	['下午','第 5 节'],['','第 6 节'],['','第 7 节'],
];
foreach($slots as $i=>$s){
	echo '<tr>';
	if($s[0]!==''){ echo '<td class="time" rowspan="4" style="font-size:22px;">'.$s[0].'</td>'; }
	echo '<td class="time">'.$s[1].'</td>';
	for($d=0;$d<5;$d++){ echo '<td></td>'; }
	echo '</tr>';
}

echo '</table></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
