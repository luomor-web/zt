<?php
/* 拼音练习 - 生成端（表单见 pinyin.html）：声母/韵母/整体认读音节四线三格描红 */
include_once dirname(__FILE__).'/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: pinyin.html");
}else{

$type=$_POST['ptype']??'all';
if(!in_array($type,['shengmu','yunmu','zhengdu','all'])){ $type='all'; }
$blank=intval($_POST['blank']??'2');
if(!in_array($blank,[1,2,3])){ $blank=2; }
$blank_html=str_repeat('<div class="eng-grid eng-blank"></div>',$blank);

$shengmu=['b','p','m','f','d','t','n','l','g','k','h','j','q','x','zh','ch','sh','r','z','c','s','y','w'];
$yunmu=['a','o','e','i','u','ü','ai','ei','ui','ao','ou','iu','ie','üe','er','an','en','in','un','ün','ang','eng','ing','ong'];
$zhengdu=['zhi','chi','shi','ri','zi','ci','si','yi','wu','yu','ye','yue','yuan','yin','yun','ying'];

$groups=[];
if($type==='shengmu'||$type==='all'){ $groups[]=['声母（23 个）',$shengmu]; }
if($type==='yunmu'||$type==='all'){ $groups[]=['韵母（24 个）',$yunmu]; }
if($type==='zhengdu'||$type==='all'){ $groups[]=['整体认读音节（16 个）',$zhengdu]; }

$type_names=['shengmu'=>'声母','yunmu'=>'韵母','zhengdu'=>'整体认读音节','all'=>'全部'];
$title='拼音练习（'.$type_names[$type].'）';

$css=xx_eng_css()
	.'.eng-wrap{width:938px;margin:0 auto;}'
	.'.eng-item{margin-bottom:18px;}'
	.'.py-group{font-size:22px;font-weight:bold;color:#666;padding:14px 4px 8px;font-family:'.xx_kaiti().';}';
echo xx_sheet_head($title,$css,'tzg');
echo '<div class="eng-wrap">';

foreach($groups as $g){
	echo '<div class="py-group">'.htmlspecialchars($g[0],ENT_QUOTES,'UTF-8').'</div>';
	foreach($g[1] as $item){
		$text=$item.' '.$item.' '.$item.' '.$item;
		echo '<div class="eng-item">';
		echo '<div class="eng-grid"><div class="eng-text">'.$text.'</div></div>';
		echo $blank_html;
		echo '</div>';
	}
}

echo '</div>';
echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
