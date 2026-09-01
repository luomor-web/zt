<?php
/* 趣味田字格模板字帖 - 生成端（表单见 picgrid.html）：图片田字格按行列排版 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: picgrid.html");
}else{

$animal=$_POST['animal']??'001';
if(!preg_match('/^0[0-7][0-9]$/',$animal)){ $animal='001'; }
$rows=intval($_POST['rows']??'10');
if(!in_array($rows,[5,8,10,12,15])){ $rows=10; }
$cols=intval($_POST['cols']??'10');
if(!in_array($cols,[6,8,10,12])){ $cols=10; }

$title='趣味田字格模板字帖';

// 单元格宽度按 938/列数 计算
$w=floor(938/$cols);
$css='li.picgrid{position:relative;background:none;border:none;height:'.$w.'px;width:'.$w.'px;}'
	.'li.picgrid img{width:100%;height:100%;display:block;object-fit:contain;}';
echo xx_sheet_head($title,$css,$bglx='tzg');
echo '<div><ul>';

$used=0;
for($r=0;$r<$rows;$r++){
	for($c=0;$c<$cols;$c++){
		echo '<li class="picgrid"><img src="img/animals/'.$animal.'.png" alt=""></li>';
	}
	// 按行高分页：每页约 1200px
	$rows_per_page=max(1,intval(1200/$w));
	if(($r+1)%$rows_per_page===0 && $r+1<$rows){
		echo "</ul></div><div class='afterpage'><ul>";
	}
}

echo '</ul></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
