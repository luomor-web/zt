<?php
/* 汉字信息汇 - 单字查询（表单见 zixun.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

$char=xx_filter_hanzi($_POST['char']??'');
if(!$char){
	header("Location: zixun.html");
}else{

// 只取第一个字
preg_match_all('/./u',$char,$m);
$char=$m[0][0];

$data=xx_load_bishun($char);
$zuci=json_decode(file_get_contents(dirname(__FILE__).'/../data/zuci.json'),true);
$py=Pinyin::getPinyin($char);

$title='汉字信息汇（'.$char.'）';
$css='.zx-wrap{width:938px;margin:0 auto;font-family:Arial,sans-serif;}'
	.'.zx-head{display:flex;align-items:center;gap:40px;padding:20px 10px;border-bottom:2px solid #ccc;margin-bottom:20px;}'
	.'.zx-char{font-size:120px;font-family:'.xx_kaiti().';line-height:1;}'
	.'.zx-meta{font-size:22px;color:#555;line-height:2;}'
	.'.zx-meta b{color:#764ba2;}'
	.'.zx-sec{font-size:20px;font-weight:bold;color:#555;padding:10px 4px;}'
	.'.zx-words{font-size:24px;color:#444;padding:0 4px 20px;line-height:2;font-family:'.xx_kaiti().';}';
echo xx_sheet_head($title,$css);

echo '<div class="zx-wrap">';
echo '<div class="zx-head">';
echo '<div class="zx-char">'.$char.'</div>';
echo '<div class="zx-meta">';
echo '<div>拼音：<b>'.htmlspecialchars($py,ENT_QUOTES,'UTF-8').'</b></div>';
if($data){ echo '<div>笔画数：<b>'.count($data['strokes']).' 画</b></div>'; }
if(isset($zuci[$char])){ echo '<div>组词：<b>'.htmlspecialchars(implode('、',$zuci[$char]),ENT_QUOTES,'UTF-8').'</b></div>'; }
echo '</div></div>';

if($data){
	echo '<div class="zx-sec">笔顺（共 '.count($data['strokes']).' 画）</div>';
	echo '<ul>';
	$row=xx_render_hanzi_row($char,'0,0,0','184,184,184',0);
	echo $row['html'];
	echo '</ul>';
}else{
	echo '<div class="zx-words">暂无该字笔顺数据。</div>';
}
echo '</div>';

echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
