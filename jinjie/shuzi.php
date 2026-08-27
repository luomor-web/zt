<?php
/* 数字字帖 - 生成端（表单见 shuzi.html）：0-9 四线三格描红 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: shuzi.html");
}else{

$mode=$_POST['mode']??'order';
if(!in_array($mode,['order','random'])){ $mode='order'; }
$num=intval($_POST['num']??'10');
if(!in_array($num,[10,20])){ $num=10; }
$blank=intval($_POST['blank']??'2');
if(!in_array($blank,[1,2,3])){ $blank=2; }
$blank_html=str_repeat('<div class="eng-grid eng-blank"></div>',$blank);

$title='数字字帖（'.($mode==='order'?'0-9 顺序':'随机').'）';

$css=xx_eng_css()
	.'.eng-wrap{width:938px;margin:0 auto;}'
	.'.eng-item{margin-bottom:18px;}';
echo xx_sheet_head($title,$css,'tzg');
echo '<div class="eng-wrap">';

if($mode==='order'){
	$digits=range(0,9);
}else{
	$digits=[];
	for($i=0;$i<$num;$i++){ $digits[]=mt_rand(0,9); }
}

foreach($digits as $d){
	$text="$d $d $d $d $d";
	echo '<div class="eng-item">';
	echo '<div class="eng-grid"><div class="eng-text">'.$text.'</div></div>';
	echo $blank_html;
	echo '</div>';
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
