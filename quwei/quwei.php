<?php
/* 趣味主题字帖 - 生成端（表单见 quwei.html）：可爱主题装饰的字词描红 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

$words=xx_filter_hanzi($_POST['words']??'');
if(!$words){
	header("Location: quwei.html");
}else{

$theme=$_POST['theme']??'animal';
if(!in_array($theme,['animal','fruit','space','ocean'])){ $theme='animal'; }

$themes=[
'animal'=>['name'=>'动物乐园','emojis'=>['🐶','🐱','🐰','🐤','🐯','🦁'],'color'=>'#ff8c42'],
'fruit'=>['name'=>'水果派对','emojis'=>['🍎','🍓','🍇','🍉','🍊','🍑'],'color'=>'#e75480'],
'space'=>['name'=>'太空探险','emojis'=>['🚀','⭐','🌙','☀️','🪐','🌟'],'color'=>'#5b7fd4'],
'ocean'=>['name'=>'海洋世界','emojis'=>['🐟','🐬','🐳','🐢','🦀','🐠'],'color'=>'#2ea6a4'],
];
$t=$themes[$theme];

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }
$py=($_POST['py']??'1')==='1';

$title='趣味字帖（'.$t['name'].'）';
$css='.qw-banner{text-align:center;font-size:30px;padding:16px 0 6px;letter-spacing:8px;}'
	.'.qw-title{text-align:center;font-size:24px;font-weight:bold;color:'.$t['color'].';padding-bottom:14px;}'
	.'.qw-deco{height:24px;border-bottom:4px dotted '.$t['color'].';margin-bottom:16px;}';
echo xx_sheet_head($title,$css,$bglx);

// 主题横幅
echo '<div class="qw-banner">'.implode(' ',$t['emojis']).'</div>';
echo '<div class="qw-title">'.$t['name'].' · 快乐练字</div>';
echo '<div class="qw-deco"></div>';
echo '<div><ul>';

preg_match_all('/./u',$words,$chars);
$used=0;
$ei=0;
foreach($chars[0] as $char){
	// 拼音格行（可选，首格放主题 emoji）
	if($py){
		echo '<li class="py" style="background:none;font-size:26px;line-height:48px;">'.$t['emojis'][$ei%count($t['emojis'])].'</li>';
		echo '<li class="py">'.htmlspecialchars(Pinyin::getPinyin($char),ENT_QUOTES,'UTF-8').'</li>';
		for($i=2;$i<12;$i++){ echo '<li class="py">&nbsp;</li>'; }
		$used+=12;
		echo xx_page_break($used);
	}
	// 描红行
	$r=xx_trace_text_row($char);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
	$ei++;
}

// 堆满整页
$rows=ceil($used/12);
$total_pages=max(1,ceil($rows/15));
$rest=$total_pages*15*12-$used;
for($i=0;$i<$rest;$i++){ echo '<li>&nbsp;</li>'; }

echo '</ul></div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
