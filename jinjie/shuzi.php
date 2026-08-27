<?php
/* 数字字帖 - 生成端（表单见 shuzi.html）：0-9 方格描红（div 行布局） */
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

$title='数字字帖（'.($mode==='order'?'0-9 顺序':'随机').'）';

$kaiti=xx_kaiti();
$css='.sz-row{display:flex;width:938px;margin:0 auto;padding-left:0;}'
	.'.sz-cell{width:80px;height:80px;margin:5px 0 5px -1px;padding-left:0;'
	.'border:1px solid #999;background:none;font-family:'.$kaiti.';'
	.'font-size:58px;text-align:center;line-height:80px;color:#c8c8c8;'
	.'-webkit-print-color-adjust:exact;print-color-adjust:exact;}';
echo xx_sheet_head($title,$css);
echo '<div>';

/* 输出一行（12 格）：$texts 为每格文字，不足补空 */
function sz_row($texts){
	$out='<div class="sz-row">';
	for($i=0;$i<12;$i++){
		$t=$texts[$i]??'&nbsp;';
		$out.='<div class="sz-cell">'.$t.'</div>';
	}
	return $out.'</div>';
}

if($mode==='order'){
	$digits=range(0,9);
}else{
	$digits=[];
	for($i=0;$i<$num;$i++){ $digits[]=mt_rand(0,9); }
}

$rowno=0;
foreach($digits as $d){
	// 描红行：数字重复 6 格 + 6 空格
	echo sz_row(array_fill(0,6,$d));
	if(++$rowno%15===0){ echo "</div><div class='afterpage'>"; }
	// 空白行
	for($b=0;$b<$blank;$b++){
		echo sz_row([]);
		if(++$rowno%15===0){ echo "</div><div class='afterpage'>"; }
	}
}

// 堆满整页
$total_pages=max(1,ceil($rowno/15));
for($i=$rowno;$i<$total_pages*15;$i++){
	echo sz_row([]);
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
