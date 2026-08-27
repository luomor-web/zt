<?php
/* 凑十法/破十法/平十法 - 生成端（表单见 coushi.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: coushi.html");
}else{

$method=$_POST['method']??'cou';
if(!in_array($method,['cou','po','ping'])){ $method='cou'; }
$count=intval($_POST['count']??'10');
if(!in_array($count,[10,20,30])){ $count=10; }

$method_names=['cou'=>'凑十法','po'=>'破十法','ping'=>'平十法'];
$title=$method_names[$method].'练习';

$css='.cs-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.cs-item{margin-bottom:26px;}'
	.'.cs-exp{font-size:32px;color:#333;font-weight:bold;}'
	.'.cs-step{font-size:22px;color:#888;margin-top:8px;}'
	.'.cs-answer{border-bottom:2px solid #999;height:44px;margin-top:10px;}';
echo xx_sheet_head($title,$css);
echo '<div class="cs-wrap">';

for($i=0;$i<$count;$i++){
	if($method==='cou'){
		// 凑十法：a+b（和 11-19），把 b 拆成 (10-a) 和 (b-(10-a))
		$a=mt_rand(2,9);
		$b=mt_rand(11-$a,9);
		$x=10-$a; $y=$b-$x;
		$exp="$a + $b =";
		$step="把 {$b} 分成 {$x} 和 {$y}，先算 {$a} + {$x} = 10，再算 10 + {$y} =（ ）";
	}elseif($method==='po'){
		// 破十法：1a - b（b>a），拆被减数 10+a，10-b+a
		$a=mt_rand(1,8);
		$beishu=10+$a;
		$b=mt_rand($a+1,9);
		$exp="$beishu - $b =";
		$step="把 {$beishu} 分成 10 和 {$a}，先算 10 - {$b} =（ ），再算（ ）+ {$a} =（ ）";
	}else{
		// 平十法：1a - b（b>a），把 b 拆成 a 和 (b-a)
		$a=mt_rand(1,8);
		$beishu=10+$a;
		$b=mt_rand($a+1,9);
		$y=$b-$a;
		$exp="$beishu - $b =";
		$step="把 {$b} 分成 {$a} 和 {$y}，先算 {$beishu} - {$a} = 10，再算 10 - {$y} =（ ）";
	}
	echo '<div class="cs-item">';
	echo '<div class="cs-exp">'.$exp.'</div>';
	echo '<div class="cs-step">'.$step.'</div>';
	echo '<div class="cs-answer"></div>';
	echo '</div>';
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
