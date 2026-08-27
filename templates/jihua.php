<?php
/* 日计划表 - 生成端（表单见 jihua.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: jihua.html");
}else{

$title='日计划表';

$css='.jh-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.jh-table{width:100%;border-collapse:collapse;}'
	.'.jh-table th{border:2px solid #999;background:#f0f0f0;padding:12px;font-size:18px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.jh-table td{border:2px solid #999;height:52px;}'
	.'.jh-table td.time{width:150px;text-align:center;font-size:16px;color:#555;}';
echo xx_sheet_head($title,$css);
echo '<div class="jh-wrap"><table class="jh-table">';
echo '<tr><th class="time">时间</th><th>计划事项</th><th style="width:130px;">完成情况</th></tr>';

$times=[];
for($h=7;$h<=21;$h++){ $times[]=sprintf('%02d:00',$h); $times[]=sprintf('%02d:30',$h); }
foreach($times as $t){
	echo '<tr><td class="time">'.$t.'</td><td></td><td></td></tr>';
}

echo '</table></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
