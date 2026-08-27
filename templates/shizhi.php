<?php
/* 五言/七言诗纸 - 生成端（表单见 shizhi.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: shizhi.html");
}else{

$type=$_POST['ptype']??'5';
if(!in_array($type,['5','7'])){ $type='5'; }
$cols=intval($type);//每句字数=列数
$rows=$_POST['rows']??'12';
$rows=intval($rows);
if(!in_array($rows,[8,12,16])){ $rows=12; }

$type_names=['5'=>'五言','7'=>'七言'];
$title=$type_names[$type].'诗纸';

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

// 每列格子宽度按 938/$cols 计算
$w=floor(938/$cols);
$kaiti=xx_kaiti();
$css='.sz-col{display:inline-block;width:'.$w.'px;vertical-align:top;}'
	.'.sz-col li{width:100%;margin:0 0 0 -1px;border-radius:0;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

// 布局：每列 $rows 个格（一列写一句诗，竖排从右往左读）
for($c=0;$c<$cols;$c++){
	echo '<li class="sz-col"><ul style="width:'.$w.'px;">';
	for($r=0;$r<$rows;$r++){ echo '<li style="width:'.$w.'px;height:'.$w.'px;line-height:'.$w.'px;">&nbsp;</li>'; }
	echo '</ul></li>';
}

echo '</ul></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
