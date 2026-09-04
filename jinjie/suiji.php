<?php
/* 随机一练 - 生成端（表单见 suiji.html）：内容每次随机 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: suiji.html");
}else{

// 纯随机抽取
function daily_pick($arr){ return $arr[array_rand($arr)]; }
function daily_pick_n($arr,$n=5){
	$copy=$arr;
	shuffle($copy);
	return array_slice($copy,0,$n);
}

$gushi=json_decode(file_get_contents(dirname(__FILE__).'/../data/gushi.json'),true);
$chengyu=json_decode(file_get_contents(dirname(__FILE__).'/../data/chengyu.json'),true);
$xiehouyu=json_decode(file_get_contents(dirname(__FILE__).'/../data/xiehouyu.json'),true);
$eng_sent=json_decode(file_get_contents(dirname(__FILE__).'/../data/english_sentences.json'),true);

// 单词来源：小学词库 / 四级 / 六级 / 考研
$src=$_POST['src']??'xx';
$src_files=['xx'=>'english_words.json','cet4'=>'cet4.json','cet6'=>'cet6.json','ky'=>'ky.json'];
$src_names=['xx'=>'小学','cet4'=>'四级','cet6'=>'六级','ky'=>'考研'];
if(!isset($src_files[$src])){ $src='xx'; }
$eng_words=json_decode(file_get_contents(dirname(__FILE__).'/../data/'.$src_files[$src]),true);

// 汉字池：hanzi.json 全量（2361 字，覆盖小学六个年级）
$hanzi_all=json_decode(file_get_contents(dirname(__FILE__).'/../data/hanzi.json'),true);
$hanzi_keys=[];
foreach($hanzi_all as $chars){ $hanzi_keys=array_merge($hanzi_keys,$chars); }

$poem=daily_pick($gushi);
$cy=daily_pick($chengyu);
$xy=daily_pick($xiehouyu);
$sent=daily_pick($eng_sent);
$words5=daily_pick_n($eng_words,5);
$hanzi5=daily_pick_n($hanzi_keys,5);

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='随机一练';
$css=xx_eng_css()
	.'.mr-sec{margin-bottom:14px;width:938px;padding-left:0;}'
	.'.mr-label{display:inline-block;background:#764ba2;color:#fff;font-size:15px;padding:3px 14px;border-radius:14px;margin-bottom:8px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.mr-text{font-size:22px;color:#333;font-family:'.xx_kaiti().';padding:4px 10px;line-height:1.9;}'
	.'.mr-sub{font-size:15px;color:#888;padding:0 10px;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div>';

// 随机一句古诗
// 诗句随机
$line=$poem['content'][array_rand($poem['content'])];
echo '<div class="mr-sec"><span class="mr-label">📜 随机一句古诗</span>';
echo '<div class="mr-text">'.htmlspecialchars($line,ENT_QUOTES,'UTF-8').'</div>';
echo '<div class="mr-sub">—— '.htmlspecialchars($poem['dynasty'].'·'.$poem['author'].'《'.$poem['title'].'》',ENT_QUOTES,'UTF-8').'</div></div>';

// 随机一句成语
echo '<div class="mr-sec"><span class="mr-label">📖 随机一句成语</span>';
echo '<div class="mr-text">'.htmlspecialchars($cy['word'],ENT_QUOTES,'UTF-8').'（'.htmlspecialchars($cy['py'],ENT_QUOTES,'UTF-8').'）</div>';
echo '<div class="mr-sub">'.htmlspecialchars($cy['meaning'],ENT_QUOTES,'UTF-8').'</div></div>';

// 随机一句歇后语
echo '<div class="mr-sec"><span class="mr-label">💬 随机一句歇后语</span>';
echo '<div class="mr-text">'.htmlspecialchars($xy['q'],ENT_QUOTES,'UTF-8').' —— '.htmlspecialchars($xy['a'],ENT_QUOTES,'UTF-8').'</div></div>';

// 随机一句英文
echo '<div class="mr-sec"><span class="mr-label">🔤 随机一句英文</span>';
echo '<div class="mr-text" style="font-family:Arial,sans-serif;">'.htmlspecialchars($sent['en'],ENT_QUOTES,'UTF-8').'</div>';
echo '<div class="mr-sub">'.htmlspecialchars($sent['zh'],ENT_QUOTES,'UTF-8').'</div></div>';

// 随机 5 个英语单词
echo '<div class="mr-sec"><span class="mr-label">📝 随机 5 个英语单词（'.$src_names[$src].'）</span>';
echo '<div class="mr-text" style="font-family:Arial,sans-serif;">';
$ws=[];
foreach($words5 as $w){ $ws[]=htmlspecialchars($w['en'],ENT_QUOTES,'UTF-8').' '.htmlspecialchars($w['zh'],ENT_QUOTES,'UTF-8'); }
echo implode('　　',$ws).'</div></div>';

// 随机 5 个汉字（描红）
echo '<div class="mr-sec"><span class="mr-label">🈶 随机 5 个汉字</span></div>';
echo '<ul>';
$used=0;
foreach($hanzi5 as $char){
	$r=xx_trace_text_row($char);
	echo $r['html'];
	$used+=$r['cells'];
	echo xx_page_break($used);
}
echo '</ul>';
echo '</div>';

echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
