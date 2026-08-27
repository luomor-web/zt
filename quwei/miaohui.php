<?php
/* 描绘画 - 生成端（表单见 miaohui.html）：简笔画线稿描摹 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: miaohui.html");
}else{

/* 简笔画线稿（viewBox 0 0 100 100，无填充，仅描边） */
$pictures=[
'sun'=>['太阳','<circle cx="50" cy="50" r="20"/><line x1="50" y1="12" x2="50" y2="24"/><line x1="50" y1="76" x2="50" y2="88"/><line x1="12" y1="50" x2="24" y2="50"/><line x1="76" y1="50" x2="88" y2="50"/><line x1="23" y1="23" x2="32" y2="32"/><line x1="68" y1="68" x2="77" y2="77"/><line x1="23" y1="77" x2="32" y2="68"/><line x1="68" y1="32" x2="77" y2="23"/>'],
'house'=>['房子','<rect x="25" y="45" width="50" height="40"/><path d="M 15 45 L 50 18 L 85 45 Z"/><rect x="43" y="62" width="14" height="23"/><rect x="32" y="52" width="10" height="10"/>'],
'flower'=>['小花','<circle cx="50" cy="42" r="8"/><circle cx="50" cy="26" r="9"/><circle cx="50" cy="58" r="9"/><circle cx="34" cy="42" r="9"/><circle cx="66" cy="42" r="9"/><line x1="50" y1="66" x2="50" y2="92"/><path d="M 50 78 Q 36 74 32 84 Q 44 88 50 78"/>'],
'cat'=>['小猫','<circle cx="50" cy="55" r="24"/><path d="M 30 42 L 26 22 L 44 34 Z"/><path d="M 70 42 L 74 22 L 56 34 Z"/><circle cx="41" cy="52" r="3"/><circle cx="59" cy="52" r="3"/><path d="M 46 62 Q 50 66 54 62"/><line x1="18" y1="56" x2="32" y2="58"/><line x1="18" y1="64" x2="32" y2="62"/><line x1="82" y1="56" x2="68" y2="58"/><line x1="82" y1="64" x2="68" y2="62"/>'],
'car'=>['汽车','<path d="M 18 60 L 24 42 L 62 42 L 74 60 Z"/><rect x="14" y="60" width="72" height="16" rx="4"/><circle cx="30" cy="78" r="8"/><circle cx="66" cy="78" r="8"/><line x1="40" y1="42" x2="40" y2="60"/><rect x="28" y="46" width="10" height="10"/>'],
'fish'=>['小鱼','<ellipse cx="46" cy="50" rx="26" ry="18"/><path d="M 72 50 L 90 36 L 90 64 Z"/><circle cx="36" cy="46" r="3"/><path d="M 46 32 Q 54 24 60 32"/><path d="M 40 66 Q 48 74 56 66"/>'],
'tree'=>['小树','<rect x="45" y="60" width="10" height="28"/><circle cx="50" cy="34" r="18"/><circle cx="32" cy="48" r="13"/><circle cx="68" cy="48" r="13"/><line x1="50" y1="88" x2="50" y2="92"/>'],
'heart'=>['爱心','<path d="M 50 82 C 20 58 14 34 32 24 C 42 18 50 26 50 34 C 50 26 58 18 68 24 C 86 34 80 58 50 82 Z"/>'],
'star'=>['星星','<path d="M 50 12 L 59 38 L 87 38 L 64 55 L 72 82 L 50 65 L 28 82 L 36 55 L 13 38 L 41 38 Z"/>'],
];

$sel=$_POST['pics']??[];
if(!is_array($sel)){ $sel=[$sel]; }
$sel=array_values(array_filter($sel,function($k)use($pictures){ return isset($pictures[$k]); }));
if(!$sel){ $sel=array_keys($pictures); }//默认全部

$title='描绘画';
$css='.mh-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:30px;width:938px;margin:0 auto;padding:10px;}'
	.'/* 覆盖全局 div{width:938px} 规则，mh-item 应占网格列的 50% */'
	.'.mh-item{width:auto;margin:0;padding-left:0;}'
	.'.mh-name{text-align:center;font-size:22px;font-weight:bold;color:#666;padding:8px 0;width:auto;margin:0;padding-left:0;}'
	.'.mh-box{border:2px solid #bbb;border-radius:8px;padding:10px;background:#fff;width:auto;margin:0;}'
	.'.mh-box svg{display:block;width:100%;height:auto;}';
echo xx_sheet_head($title,$css);
echo '<div class="mh-grid">';

foreach($sel as $key){
	$p=$pictures[$key];
	echo '<div class="mh-item">';
	echo '<div class="mh-name">'.$p[0].'</div>';
	// 描摹图（虚线浅蓝）
	echo '<div class="mh-box"><svg viewBox="0 0 100 100"><g fill="none" stroke="#8db4e2" stroke-width="2.5" stroke-dasharray="6,4" stroke-linecap="round" stroke-linejoin="round">'.$p[1].'</g></svg></div>';
	// 空白临摹框
	echo '<div class="mh-box" style="margin-top:10px;height:200px;"></div>';
	echo '</div>';
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
