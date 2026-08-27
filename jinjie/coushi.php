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

/* 单个竖式（答案横线留白；$a 可传 '□' 表示待填） */
function cs_vertical($a,$sign,$b){
	return '<div class="ss-item">'
		.'<div class="row"><span class="num">'.$a.'</span></div>'
		.'<div class="row"><span class="op">'.$sign.'</span><span class="num">'.$b.'</span></div>'
		.'<div class="line"></div>'
		.'</div>';
}

$css='.cs-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.cs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:40px 30px;}'
	.'.cs-item{font-family:"Courier New",monospace;}'
	.'.cs-exp{font-size:30px;color:#333;font-weight:bold;}'
	.'.cs-hint{font-size:18px;color:#888;margin:6px 0 10px;font-family:Arial,sans-serif;}'
	.'.cs-v{display:flex;gap:50px;}'
	.'.ss-item{font-size:26px;line-height:1.5;width:110px;}'
	.'.ss-item .row{display:flex;justify-content:flex-end;letter-spacing:2px;}'
	.'.ss-item .row .op{margin-right:auto;}'
	.'.ss-item .row .num{text-align:right;}'
	.'.ss-item .line{border-top:2px solid #333;margin-top:4px;height:30px;}';
echo xx_sheet_head($title,$css);
echo '<div class="cs-wrap"><div class="cs-grid">';

for($i=0;$i<$count;$i++){
	if($method==='cou'){
		// 凑十法：a+b（和 11-19），竖式1：a+x 凑十；竖式2：10+y
		$a=mt_rand(2,9);
		$b=mt_rand(11-$a,9);
		$x=10-$a; $y=$b-$x;
		$exp="{$a} + {$b} =";
		$hint="把 {$b} 分成 {$x} 和 {$y}";
		$v1=cs_vertical($a,'+',$x);
		$v2=cs_vertical(10,'+',$y);
	}elseif($method==='po'){
		// 破十法：N-b（N=10+a，b>a），竖式1：10-b；竖式2：□+a
		$a=mt_rand(1,8);
		$N=10+$a;
		$b=mt_rand($a+1,9);
		$exp="{$N} - {$b} =";
		$hint="把 {$N} 分成 10 和 {$a}";
		$v1=cs_vertical(10,'-',$b);
		$v2=cs_vertical('□','+',$a);
	}else{
		// 平十法：N-b（b>a，b 拆成 a 和 y），竖式1：N-a=10；竖式2：10-y
		$a=mt_rand(1,8);
		$N=10+$a;
		$b=mt_rand($a+1,9);
		$y=$b-$a;
		$exp="{$N} - {$b} =";
		$hint="把 {$b} 分成 {$a} 和 {$y}";
		$v1=cs_vertical($N,'-',$a);
		$v2=cs_vertical(10,'-',$y);
	}
	echo '<div class="cs-item">';
	echo '<div class="cs-exp">'.$exp.'</div>';
	echo '<div class="cs-hint">'.$hint.'</div>';
	echo '<div class="cs-v">'.$v1.$v2.'</div>';
	echo '</div>';
}

echo '</div></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
