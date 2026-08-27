<?php
/* 控笔练习 - 生成端（表单见 kongbi.html）：曲线描红训练运笔 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: kongbi.html");
}else{

$type=$_POST['ptype']??'wave';
if(!in_array($type,['wave','zigzag','slant','spiral'])){ $type='wave'; }
$rows=intval($_POST['rows']??'10');
if(!in_array($rows,[5,10,15])){ $rows=10; }

$type_names=['wave'=>'波浪线','zigzag'=>'锯齿线','slant'=>'斜线组','spiral'=>'螺旋线'];
$title='控笔练习（'.$type_names[$type].'）';

/* 生成一行的 SVG 路径（行宽 900，高 90） */
function kongbi_path($type){
	$d='';
	switch($type){
	case 'wave':
		$d='M 0 45';
		for($x=0;$x<900;$x+=150){ $d.=" Q ".($x+37)." 5 ".($x+75)." 45 Q ".($x+112)." 85 ".($x+150)." 45"; }
		break;
	case 'zigzag':
		$d='M 0 70';
		for($x=0;$x<900;$x+=60){ $d.=" L ".($x+30)." 20 L ".($x+60)." 70"; }
		break;
	case 'slant':
		for($x=0;$x<900;$x+=45){ $d.="M ".($x)." 80 L ".($x+25)." 10 "; }
		break;
	case 'spiral':
		$d='M 60 45';
		for($g=0;$g<9;$g++){
			$cx=60+$g*100;
			$d.=" m 0 0 a 35 35 0 1 1 70 0 a 25 25 0 1 1 -50 0";
		}
		break;
	}
	return $d;
}

$css='.kb-row{width:938px;margin:0 auto 30px;height:100px;background:url(../img/tzg.svg);background-size:80px 80px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.kb-row svg{display:block;margin:0 auto;}';
echo xx_sheet_head($title,$css);
echo '<div>';

$path=kongbi_path($type);
for($i=0;$i<$rows;$i++){
	echo '<div class="kb-row">';
	echo '<svg width="900" height="90"><path d="'.$path.'" stroke="#b8b8b8" stroke-width="2.5" fill="none"/></svg>';
	echo '</div>';
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
