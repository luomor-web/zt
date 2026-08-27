<?php
/* 点阵纸 - 生成端（表单见 dianzhen.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: dianzhen.html");
}else{

$size=$_POST['size']??'mid';
if(!in_array($size,['small','mid','large'])){ $size='mid'; }
$conf=[
	'small'=>['gap'=>24,'dot'=>3,'rows'=>34,'cols'=>36],
	'mid'=>['gap'=>32,'dot'=>4,'rows'=>26,'cols'=>27],
	'large'=>['gap'=>44,'dot'=>5,'rows'=>19,'cols'=>20],
];
$c=$conf[$size];

$size_names=['small'=>'小间距','mid'=>'中间距','large'=>'大间距'];
$title='点阵纸（'.$size_names[$size].'）';

$css='.dz-wrap{width:938px;margin:0 auto;padding:10px;}'
	.'.dz-row{display:flex;justify-content:space-between;margin:0;padding-left:0;}'
	.'.dz-dot{width:'.$c['dot'].'px;height:'.$c['dot'].'px;border-radius:50%;background:#aaa;margin:0;padding-left:0;'
	.'-webkit-print-color-adjust:exact;print-color-adjust:exact;}';
echo xx_sheet_head($title,$css);
echo '<div class="dz-wrap">';
for($r=0;$r<$c['rows'];$r++){
	echo '<div class="dz-row" style="margin-bottom:'.$c['gap'].'px;">';
	for($col=0;$col<$c['cols'];$col++){ echo '<div class="dz-dot"></div>'; }
	echo '</div>';
}
echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
