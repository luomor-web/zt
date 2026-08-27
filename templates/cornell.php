<?php
/* 康奈尔笔记纸 - 生成端（表单见 cornell.html） */
include_once dirname(__FILE__).'/../xiaoxue/lib.php';

if($_SERVER['REQUEST_METHOD']!=='POST'){
	header("Location: cornell.html");
}else{

$titlebar=($_POST['titlebar']??'1')==='1';//顶部标题栏
$title='康奈尔笔记纸';

$css='.cn-wrap{width:938px;margin:0 auto;}'
	.'.cn-title{border:2px solid #999;border-bottom:none;height:70px;line-height:70px;padding-left:20px;font-size:20px;color:#aaa;}'
	.'.cn-main{display:flex;border:2px solid #999;}'
	.'.cn-cue{width:250px;border-right:2px solid #999;}'
	.'.cn-notes{flex:1;}'
	.'.cn-line{height:56px;border-bottom:1px solid #ccc;margin:0;padding-left:0;}'
	.'.cn-cue .cn-line:nth-child(even){background:none;}'
	.'.cn-summary{border:2px solid #999;border-top:none;height:150px;padding:10px 16px;color:#aaa;font-size:16px;}'
	.'.cn-label{color:#bbb;font-size:14px;padding:6px 10px;margin:0;}';
echo xx_sheet_head($title,$css);
echo '<div class="cn-wrap">';

if($titlebar){
	echo '<div class="cn-title">科目：　　　　　主题：　　　　　日期：</div>';
}
echo '<div class="cn-main">';
echo '<div class="cn-cue"><div class="cn-label">线索栏</div>';
for($i=0;$i<12;$i++){ echo '<div class="cn-line"></div>'; }
echo '</div>';
echo '<div class="cn-notes"><div class="cn-label">笔记栏</div>';
for($i=0;$i<12;$i++){ echo '<div class="cn-line"></div>'; }
echo '</div>';
echo '</div>';
echo '<div class="cn-summary"><span class="cn-label" style="padding:0;">总结</span></div>';
echo '</div>';

echo xx_auto_print($title,xx_student_info());
echo '</body></html>';

}
