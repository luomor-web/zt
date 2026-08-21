<?php
/* 数学题练习 - 生成端（表单见 math.html）：加减乘除、比大小，10/20/100 以内 */
include_once dirname(__FILE__).'/lib.php';

/* 生成一道题，返回算式字符串（不含答案）；$carry: any=随机 carry=进位/退位 none=不进位/不退位 */
function xx_math_item($type,$max,$carry='any'){
	switch($type){
	case 'add':// 加法，和不超过 $max；carry 控制个位是否进位
		for($try=0;$try<60;$try++){
			$a=mt_rand(1,$max-1);
			$b=mt_rand(1,$max-$a);
			if($max<=10 || $carry==='any'){ break; }// 10 以内无进位概念
			$has_carry=(($a%10)+($b%10))>=10;
			if(($carry==='carry')===$has_carry){ break; }
		}
		return "$a + $b =";
	case 'sub':// 减法，结果非负；carry 控制个位是否退位
		for($try=0;$try<60;$try++){
			$a=mt_rand(1,$max);
			$b=mt_rand(0,$a);
			if($max<=10 || $carry==='any'){ break; }
			$has_borrow=($a%10)<($b%10);
			if(($carry==='carry')===$has_borrow){ break; }
		}
		return "$a - $b =";
	case 'mul':// 乘法
		if($max<=10){ $a=mt_rand(1,5); $b=mt_rand(1,2); }
		elseif($max<=20){ $a=mt_rand(1,9); $b=mt_rand(1,2); }
		else{ $a=mt_rand(1,9); $b=mt_rand(1,9); }// 表内乘法
		if($a*$b>$max*10){ $a=mt_rand(1,9); $b=mt_rand(1,9); }
		return "$a × $b =";
	case 'div':// 除法，整除
		if($max<=10){ $b=mt_rand(1,2); $q=mt_rand(1,5); }
		elseif($max<=20){ $b=mt_rand(1,4); $q=mt_rand(1,5); }
		else{ $b=mt_rand(1,9); $q=mt_rand(1,9); }
		$a=$b*$q;
		return "$a ÷ $b =";
	case 'cmp':// 比大小
	default:
		$a=mt_rand(0,$max);
		$b=mt_rand(0,$max);
		return "$a ○ $b";
	}
}

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: math.html");
}else{

$type=$_POST['op']??'add';
if(!in_array($type,['add','sub','mul','div','cmp','mix'])){ $type='add'; }
$carry=$_POST['carry']??'any';//进位/退位（仅加减法）
if(!in_array($carry,['any','carry','none'])){ $carry='any'; }
$max=intval($_POST['range']??'10');
if(!in_array($max,[10,20,100])){ $max=10; }
$count=intval($_POST['count']??'40');
if(!in_array($count,[20,40,60])){ $count=40; }
$cols=intval($_POST['cols']??'4');
if(!in_array($cols,[3,4,5])){ $cols=4; }

$op_names=['add'=>'加法','sub'=>'减法','mul'=>'乘法','div'=>'除法','cmp'=>'比大小','mix'=>'混合运算'];
$title='数学练习（'.$op_names[$type].' '.$max.' 以内）';

$css='.math-wrap{width:938px;margin:0 auto;font-family:"Times New Roman",Arial,sans-serif;}'
	.'.math-table{width:100%;border-collapse:collapse;}'
	.'.math-table td{font-size:30px;padding:26px 10px;color:#333;white-space:nowrap;}';
echo xx_sheet_head($title,$css);
echo '<div class="math-wrap"><table class="math-table">';

$types=$type==='mix' ? ['add','sub','mul','div'] : [$type];
for($i=0;$i<$count;$i++){
	if($i%$cols===0){ echo '<tr>'; }
	$t=$types[array_rand($types)];
	echo '<td>'.xx_math_item($t,$max,$carry).'</td>';
	if($i%$cols===$cols-1){ echo '</tr>'; }
}
// 补齐最后一行
$rest=$count%$cols;
if($rest){ for($i=$rest;$i<$cols;$i++){ echo '<td></td>'; } echo '</tr>'; }

echo '</table></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
