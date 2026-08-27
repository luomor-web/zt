<?php
/* 竖式计算 - 生成端（表单见 shushi.html）：加减乘除竖式 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: shushi.html");
}else{

$op=$_POST['op']??'add';
if(!in_array($op,['add','sub','mul','div'])){ $op='add'; }
$wei=intval($_POST['wei']??'2');//位数（较大数的位数）
if(!in_array($wei,[1,2,3])){ $wei=2; }
$count=intval($_POST['count']??'20');
if(!in_array($count,[20,30,40])){ $count=20; }

$min=pow(10,$wei-1); $maxn=pow(10,$wei)-1;

$op_names=['add'=>'加法','sub'=>'减法','mul'=>'乘法','div'=>'除法'];
$op_signs=['add'=>'+','sub'=>'-','mul'=>'×','div'=>'÷'];
$title='竖式计算（'.$op_names[$op].' '.$wei.' 位数）';

$css='.ss-wrap{width:938px;margin:0 auto;font-family:"Courier New",monospace;}'
	.'.ss-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:36px 20px;padding:20px 10px;}'
	.'.ss-item{font-size:30px;line-height:1.5;width:130px;margin:0 auto;}'
	/* 覆盖全局 div{width:938px} 规则，否则行被撑宽导致数字错位 */
	.'.ss-item .row{display:flex;justify-content:flex-end;letter-spacing:2px;width:auto;margin:0;padding-left:0;}'
	.'.ss-item .row .op{margin-right:auto;}'
	.'.ss-item .row .num{text-align:right;}'
	.'.ss-item .line{border-top:2px solid #333;margin-top:4px;height:34px;width:auto;padding-left:0;}';
echo xx_sheet_head($title,$css);
echo '<div class="ss-wrap"><div class="ss-grid">';

for($i=0;$i<$count;$i++){
	switch($op){
	case 'add':
		$a=mt_rand($min,$maxn); $b=mt_rand(1,$maxn);
		break;
	case 'sub':
		$a=mt_rand($min,$maxn); $b=mt_rand(1,$a);//非负
		break;
	case 'mul':
		$a=mt_rand($min,$maxn); $b=mt_rand(2,9);
		break;
	case 'div':
		$b=mt_rand(2,9); $q=mt_rand($min,min($maxn,99));
		$a=$b*$q;//整除
		break;
	}
	echo '<div class="ss-item">';
	echo '<div class="row"><span class="num">'.$a.'</span></div>';
	echo '<div class="row"><span class="op">'.$op_signs[$op].'</span><span class="num">'.$b.'</span></div>';
	echo '<div class="line"></div>';
	echo '</div>';
}

echo '</div></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
