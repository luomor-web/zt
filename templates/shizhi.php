<?php
/* 五言/七言诗纸 - 生成端（表单见 shizhi.html）：每行 5/7 个田字格 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: shizhi.html");
}else{

$type=$_POST['ptype']??'5';
if(!in_array($type,['5','7'])){ $type='5'; }
$cols=intval($type);//每行格数=每句字数
$rows=intval($_POST['rows']??'12');
if(!in_array($rows,[8,12,16])){ $rows=12; }

$type_names=['5'=>'五言','7'=>'七言'];
$title=$type_names[$type].'诗纸';

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

// 行布局：每行 $cols 个格（80px），居中排列
$css='.sz-row{display:flex;justify-content:center;width:938px;margin:0 auto;padding-left:0;}'
	.'.sz-row li{margin:5px 0 5px -1px;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul style="width:938px;padding-left:0;">';

for($r=0;$r<$rows;$r++){
	echo '<div class="sz-row">';
	for($c=0;$c<$cols;$c++){ echo '<li>&nbsp;</li>'; }
	echo '</div>';
	// 每 15 行分页
	if(($r+1)%15===0 && $r+1<$rows){ echo "</ul></div><div class='afterpage'><ul style='width:938px;padding-left:0;'>"; }
}

echo '</ul></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
