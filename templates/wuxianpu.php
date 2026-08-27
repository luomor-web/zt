<?php
/* 五线谱纸 - 生成端（表单见 wuxianpu.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: wuxianpu.html");
}else{

$rows=$_POST['rows']??'10';
$rows=intval($rows);
if(!in_array($rows,[8,10,12])){ $rows=10; }

$title='五线谱纸';

$css='.wx-wrap{width:938px;margin:0 auto;padding:10px 0;}'
	.'.wx-sys{margin-bottom:44px;padding-left:0;}'
	.'.wx-line{height:10px;border-bottom:2px solid #555;margin:0;padding-left:0;}'
	.'.wx-line:first-child{border-top:2px solid #555;}';
echo xx_sheet_head($title,$css);
echo '<div class="wx-wrap">';
for($i=0;$i<$rows;$i++){
	echo '<div class="wx-sys">';
	for($l=0;$l<5;$l++){ echo '<div class="wx-line"></div>'; }
	echo '</div>';
}
echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
