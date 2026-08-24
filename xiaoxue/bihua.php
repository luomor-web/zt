<?php
/* 笔画练习 - 生成端（表单见 bihua.html）：笔画名称 + 例字描红 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: bihua.html");
}else{

/* 基本笔画：名称、拼音、例字 */
$strokes=[
['横','héng','一 二 三'],
['竖','shù','十 中 丰'],
['撇','piě','人 八 大'],
['捺','nà','木 天 入'],
['点','diǎn','主 六 文'],
['提','tí','打 地 江'],
['横折','héng zhé','口 五 日'],
['横钩','héng gōu','写 买 皮'],
['竖钩','shù gōu','小 可 水'],
['竖弯钩','shù wān gōu','儿 元 见'],
['弯钩','wān gōu','了 子 狗'],
['斜钩','xié gōu','我 找 成'],
['卧钩','wò gōu','心 必 思'],
['横撇','héng piě','又 水 友'],
['撇折','piě zhé','去 云 车'],
['撇点','piě diǎn','女 好 妈'],
['横折钩','héng zhé gōu','月 力 用'],
['竖折','shù zhé','山 出 区'],
['竖提','shù tí','长 民 衣'],
['横折弯钩','héng zhé wān gōu','九 几 吃'],
];

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='笔画练习';
$css='.bh-name{font-size:22px;font-weight:bold;color:#666;padding:12px 4px 4px;font-family:'.xx_kaiti().';}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div><ul>';

$used=0;
foreach($strokes as $s){
	// 名称行（整行通栏文字）
	echo '<li style="background:none;height:auto;width:100%;text-align:left;font-size:18px;color:#555;line-height:2;padding-left:4px;font-family:'.xx_kaiti().';">'
		.htmlspecialchars($s[0].'（'.$s[1].'）',ENT_QUOTES,'UTF-8')
		.'<span style="color:#999;font-size:14px;">　例字：'.htmlspecialchars($s[2],ENT_QUOTES,'UTF-8').'</span></li>';
	// 例字描红行：例字重复两遍
	$trace=str_replace(' ','',$s[2]).str_replace(' ','',$s[2]);
	$r=xx_trace_text_row($trace);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
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
