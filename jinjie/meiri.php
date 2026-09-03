<?php
/* 每日一练 - 生成端（表单见 meiri.html）：按日期轮换的每日内容 */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: meiri.html");
}else{

// 日期（默认今天），格式 YYYY-MM-DD
$date=$_POST['date']??date('Y-m-d');
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)){ $date=date('Y-m-d'); }
$daynum=(int)((strtotime($date)-strtotime('2000-01-01'))/86400);//天数序号

/* 按日轮换取一条：天数序号与总数互质步长，分布均匀 */
function daily_pick($arr,$daynum,$step=7){
	$n=count($arr);
	return $arr[($daynum*$step)%$n];
}
/* 按日轮换取连续 N 条 */
function daily_pick_n($arr,$daynum,$n=5){
	$count=count($arr);
	$start=($daynum*13)%$count;
	$out=[];
	for($i=0;$i<$n;$i++){ $out[]=$arr[($start+$i)%$count]; }
	return $out;
}

$gushi=json_decode(file_get_contents(dirname(__FILE__).'/../data/gushi.json'),true);
$chengyu=json_decode(file_get_contents(dirname(__FILE__).'/../data/chengyu.json'),true);
$xiehouyu=json_decode(file_get_contents(dirname(__FILE__).'/../data/xiehouyu.json'),true);
$eng_sent=json_decode(file_get_contents(dirname(__FILE__).'/../data/english_sentences.json'),true);
$eng_words=json_decode(file_get_contents(dirname(__FILE__).'/../data/english_words.json'),true);
$zuci=json_decode(file_get_contents(dirname(__FILE__).'/../data/zuci.json'),true);
$hanzi_keys=array_keys($zuci);

$poem=daily_pick($gushi,$daynum);
$cy=daily_pick($chengyu,$daynum);
$xy=daily_pick($xiehouyu,$daynum);
$sent=daily_pick($eng_sent,$daynum);
$words5=daily_pick_n($eng_words,$daynum,5);
$hanzi5=daily_pick_n($hanzi_keys,$daynum,5);

$bglx=$_POST['types']??'tzg';
if(!in_array($bglx,['tzg','mzg'])){ $bglx='tzg'; }
$bgcolor=$_POST['bgcolor']??'black';
if($bgcolor!=='black'){ $bglx.=$bgcolor; }

$title='每日一练（'.$date.'）';
$css=xx_eng_css()
	.'.mr-sec{margin-bottom:14px;width:938px;padding-left:0;}'
	.'.mr-label{display:inline-block;background:#764ba2;color:#fff;font-size:15px;padding:3px 14px;border-radius:14px;margin-bottom:8px;-webkit-print-color-adjust:exact;print-color-adjust:exact;}'
	.'.mr-text{font-size:22px;color:#333;font-family:'.xx_kaiti().';padding:4px 10px;line-height:1.9;}'
	.'.mr-sub{font-size:15px;color:#888;padding:0 10px;}';
echo xx_sheet_head($title,$css,$bglx);
echo '<div>';

// 每日一句古诗
// 诗句也按日期确定（同一天内容固定）
$line=$poem['content'][$daynum % count($poem['content'])];
echo '<div class="mr-sec"><span class="mr-label">📜 每日一句古诗</span>';
echo '<div class="mr-text">'.htmlspecialchars($line,ENT_QUOTES,'UTF-8').'</div>';
echo '<div class="mr-sub">—— '.htmlspecialchars($poem['dynasty'].'·'.$poem['author'].'《'.$poem['title'].'》',ENT_QUOTES,'UTF-8').'</div></div>';

// 每日一句成语
echo '<div class="mr-sec"><span class="mr-label">📖 每日一句成语</span>';
echo '<div class="mr-text">'.htmlspecialchars($cy['word'],ENT_QUOTES,'UTF-8').'（'.htmlspecialchars($cy['py'],ENT_QUOTES,'UTF-8').'）</div>';
echo '<div class="mr-sub">'.htmlspecialchars($cy['meaning'],ENT_QUOTES,'UTF-8').'</div></div>';

// 每日一句歇后语
echo '<div class="mr-sec"><span class="mr-label">💬 每日一句歇后语</span>';
echo '<div class="mr-text">'.htmlspecialchars($xy['q'],ENT_QUOTES,'UTF-8').' —— '.htmlspecialchars($xy['a'],ENT_QUOTES,'UTF-8').'</div></div>';

// 每日一句英文
echo '<div class="mr-sec"><span class="mr-label">🔤 每日一句英文</span>';
echo '<div class="mr-text" style="font-family:Arial,sans-serif;">'.htmlspecialchars($sent['en'],ENT_QUOTES,'UTF-8').'</div>';
echo '<div class="mr-sub">'.htmlspecialchars($sent['zh'],ENT_QUOTES,'UTF-8').'</div></div>';

// 每日 5 个英语单词
echo '<div class="mr-sec"><span class="mr-label">📝 每日 5 个英语单词</span>';
echo '<div class="mr-text" style="font-family:Arial,sans-serif;">';
$ws=[];
foreach($words5 as $w){ $ws[]=htmlspecialchars($w['en'],ENT_QUOTES,'UTF-8').' '.htmlspecialchars($w['zh'],ENT_QUOTES,'UTF-8'); }
echo implode('　　',$ws).'</div></div>';

// 每日 5 个汉字（描红）
echo '<div class="mr-sec"><span class="mr-label">🈶 每日 5 个汉字</span></div>';
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
