<?php
//error_reporting(0);
include_once 'Pinyin.php';

$words=$_POST['words']??'';//笔顺字

$bglx=$_POST['types']??'tzg';//表格类型，默认田字格
$bgcolor=$_POST['bgcolor']??'black';//表格颜色

if($_POST['bgcolor']!='black'){
	$bglx=$bglx.$bgcolor;//表格颜色变化
}

$z_color=$_POST['zcolor']??'black';//主字体颜色
$f_color=$_POST['fcolor']??'5';//辅字体颜色
$title=$_POST['title']??'';//辅字体颜色
$bs=$_POST['bs']??'0';//笔顺填充
$py=$_POST['py']??'0';//笔顺填充

$lang=$_POST['lang']??($_COOKIE['tzg_lang']??'zh');//界面语言
if(!in_array($lang,['zh','zh-TW','en'],true)){ $lang='zh'; }

/*界面文案翻译（新增语言时在此追加）*/
$T=[
'zh'=>['sheetTitle'=>'田字格字帖生成器','defaultHeader'=>'田字格字帖生成器','htmlLang'=>'zh-CN','classLabel'=>'班级','nameLabel'=>'姓名','dateLabel'=>'日期','printBtn'=>'🖨️ 打印','saveBtn'=>'💾 保存图片','closeBtn'=>'✕ 关闭','saveFail'=>'保存失败，请截屏保存'],
'zh-TW'=>['sheetTitle'=>'田字格字帖產生器','defaultHeader'=>'田字格字帖產生器','htmlLang'=>'zh-TW','classLabel'=>'班級','nameLabel'=>'姓名','dateLabel'=>'日期','printBtn'=>'🖨️ 列印','saveBtn'=>'💾 保存圖片','closeBtn'=>'✕ 關閉','saveFail'=>'保存失敗，請截圖保存'],
'en'=>['sheetTitle'=>'Chinese Character Practice Sheet','defaultHeader'=>'Chinese Practice Sheet','htmlLang'=>'en','classLabel'=>'Class','nameLabel'=>'Name','dateLabel'=>'Date','printBtn'=>'🖨️ Print','saveBtn'=>'💾 Save Image','closeBtn'=>'✕ Close','saveFail'=>'Save failed, please take a screenshot'],
];

if(trim($title)===''){ $title=$T[$lang]['defaultHeader']; }

/*班级、姓名、日期（打印页头信息行，留空显示下划线供手写）*/
$class=trim($_POST['class']??'');
$name=trim($_POST['name']??'');
$colon=$lang==='en'?': ':'：';
$blank=$lang==='en'?'________':'＿＿＿＿';
$info=$T[$lang]['classLabel'].$colon.($class!==''?htmlspecialchars($class,ENT_QUOTES,'UTF-8'):$blank)
	.'　'.$T[$lang]['nameLabel'].$colon.($name!==''?htmlspecialchars($name,ENT_QUOTES,'UTF-8'):$blank)
	.'　'.$T[$lang]['dateLabel'].$colon.$blank;

/*过滤掉非中文*/
preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $words, $words);
$words = implode('', $words[0] );


//没有文字，跳转
if(!$words){
	header("Location: /");
	exit();
}

/*主字体颜色*/
$color=[
'green'=>'0,176,80',//绿色
'black'=>'0,0,0',//黑色
'red'=>'152,15,41',//红色
];

/*辅字体颜色*/
$fz_color=[
'10'=>'255,255,255',//白色

'green1'=>'136,255,136',//绿色1
'green2'=>'153,255,153',//绿色2
'green3'=>'160,255,160',//绿色3
'green4'=>'170,255,170',//绿色4
'green5'=>'184,255,184',//绿色5
'green6'=>'204,255,204',//绿色6

'black1'=>'136,136,136',//黑色1
'black2'=>'153,153,153',//黑色2
'black3'=>'160,160,160',//黑色3
'black4'=>'170,170,170',//黑色4
'black5'=>'184,184,184',//黑色5
'black6'=>'204,204,204',//黑色6

'red1'=>'255,136,136',//红色1
'red2'=>'255,153,153',//红色2
'red3'=>'255,160,160',//红色3
'red4'=>'255,170,170',//红色4
'red5'=>'255,184,184',//红色5
'red6'=>'255,204,204',//红色6
];

$color=$color[$z_color];//显示主颜色

$fcolor=$fz_color[$z_color.$f_color];//辅助颜色

if($f_color=='10'){
	$fcolor=$fz_color['10'];
}
?><!doctype html>
<html lang="<?=$T[$lang]['htmlLang'];?>">
<head>
<meta charset="utf-8">
<title><?=htmlspecialchars($T[$lang]['sheetTitle'],ENT_QUOTES,'UTF-8');?></title>
<style>
body,div,p,ul,li{ padding:0; margin:0; list-style:none;}
body{ padding-top:60px; }/*屏幕显示时给顶部悬浮按钮留位*/
div{ width:938px; margin:0 auto;padding-left:2px; }
li{display: inline-block; width:80px; height:80px; font-family:"楷体","楷体_gb2312", "Kaiti SC", STKaiti, "AR PL UKai CN", "AR PL UKai HK", "AR PL UKai TW", "AR PL UKai TW MBE", "AR PL KaitiM GB", KaiTi, KaiTi_GB2312, DFKai-SB, "TW\-Kai"; font-size:58px; text-align:center; line-height:85px; background:url(img/<?=$bglx;?>.svg); background-size:80px 80px; -webkit-print-color-adjust:exact; print-color-adjust:exact; margin:5px 0px 5px -2px; color:#b8b8b8; }
li.f{color:#000;margin-left:-0px}
li.svg{line-height:84px;}
li svg{ magin:8px; vertical-align:middle;}
li.py{ height:48px; line-height:54px; font-size:22px; color:#555; font-family:Arial,"Helvetica Neue",sans-serif;
	-webkit-print-color-adjust:exact; print-color-adjust:exact;
	background-image:
	 linear-gradient(#999,#999),
	 repeating-linear-gradient(to right,#bbb 0,#bbb 5px,transparent 5px,transparent 10px),
	 repeating-linear-gradient(to right,#bbb 0,#bbb 5px,transparent 5px,transparent 10px),
	 linear-gradient(#999,#999);
	background-size:100% 1px,100% 1px,100% 1px,100% 2px;
	background-position:0 0,0 15px,0 31px,0 46px;
	background-repeat:no-repeat; }
.afterpage{ page-break-before:always;}
.afterpage{ page-break-before:always;}
.page-head{height: 76px;line-height: 96px; font-size: 32px;text-align: center;color: #666666}
.page-info{height: 40px;line-height: 40px; font-size: 16px;text-align: center;color: #666666}
.print-tools{position:fixed;top:14px;left:0;right:0;width:auto;margin:0;padding:0;display:flex;justify-content:center;gap:8px;z-index:999;}
.print-tools button{padding:8px 20px;font-size:14px;border:none;border-radius:20px;cursor:pointer;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.2);}
.print-tools .btn-print{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);}
.print-tools .btn-save{background:#27ae60;}
.print-tools .btn-close{background:#999;}
@media print{body{padding-top:0;}.afterpage{ page-break-before:always;}.print-tools{display: none;}}
@page {size: auto;margin: 5mm 16mm 5mm 16mm;}
</style>
</head>
<body>
<div class="print-tools">
<button type="button" class="btn-print" onclick="window.print()"><?=$T[$lang]['printBtn'];?></button>
<button type="button" class="btn-save" onclick="saveAsImage()"><?=$T[$lang]['saveBtn'];?></button>
<button type="button" class="btn-close" onclick="window.close()"><?=$T[$lang]['closeBtn'];?></button>
</div>
<div>
<ul>
<?php



preg_match_all("/./u",$words,$hz);

for($ihz=0;$ihz<count($hz['0']);$ihz++){

	$hzGBK=iconv('UTF-8', 'GB2312' ,$hz['0'][$ihz]); 

	if(file_exists("bishun_data/".$hzGBK.".json")){
		$data=file_get_contents("bishun_data/".$hzGBK.".json");
	}else{
		$data=file_get_contents("bishun_data/".$hz['0'][$ihz].".json");
	}

	$data=json_decode($data,1);
	$count=count($data['strokes']);//统计共有多少画
	
	/*拼音独占一行（四线三格拼音格）*/
	if($py)
	{
		$py_str=Pinyin::getPinyin($hz['0'][$ihz]);
		echo '<li class="py">'.htmlspecialchars($py_str,ENT_QUOTES,'UTF-8').'</li>';
		for($pi=1;$pi<12;$pi++){ echo '<li class="py">&nbsp;</li>'; }
	}

	/*显示完整字符*/
	echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';

	foreach ($data['strokes'] as $v){
		echo '<path d="'.$v.'"style="fill:rgb('.$color.');stroke:rgb('.$color.');" stroke-width = "0"></path>';
	}

	echo "</g></svg></li>";


	//按笔数显示
	for($i=0;$i<$count;$i++){
		
		echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
		
		for($ii=0;$ii<=$i;$ii++){
			echo '<path d="'.$data['strokes'][$ii].'"style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width = "0"></path>';
		}
		

		echo '</g></svg></li>';

	}
	
	
	/*判断是否填充12个田字格*/
	$tzg12=($count+1)/12;
	$kg=0;//空格，每行剩余未填充的空格
	if(!is_int($tzg12)){
		$kg=12- (12* $tzg12);
	}
	//为负数
	if($kg<0){
		$kg= ((ceil(abs($kg)/12)+1)*12)-($count+1);
	}
	
	/*行数不够，填充*/
	//填充完整字符
	if($kg and $bs){
		for($i=0;$i<$kg;$i++){
			/*显示完整字符*/
		 echo '<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
	
	     foreach ($data['strokes'] as $v){
		    echo '<path d="'.$v.'"style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width = "0"></path>';
	     }
		 echo "</g></svg></li>";

		}
	}
	//填充空行
	if($kg and !$bs){
		for($i=0;$i<$kg;$i++){
			echo '<li class="svg">&nbsp;</li>';
		}
		
	}

	/*分页显示标题头部（拼音格行也计入行数）*/

	$tzg_hs[]= ceil($tzg12)+($py?1:0);//占用行数
	$arraytzg=intval(array_sum($tzg_hs));
	$arraytzg=$arraytzg/15;
	if(is_int($arraytzg)){
		echo "</ul></div><div class='afterpage'><ul>";
	}

}

//堆满整页
$tzg_hs=array_sum($tzg_hs);//田字格使用行数
$tzgzys=ceil($tzg_hs/15);//田字格总页数
$zhengye=($tzgzys*15-$tzg_hs)*12;

	for($i=0;$i<$zhengye;$i++){
		echo "<li>&nbsp;</li>";
	}

?>
</ul>
</div>
<div style="display: none;">

</div>
<div id="page-head-box" style="display: none;">
<div class="page-head"><?=$title;?></div>
<div class="page-info"><?=$info;?></div>
</div>

<script src="https://ajax.aspnetcdn.com/ajax/jquery/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script type="text/javascript">
    $('body').prepend($('#page-head-box').html());
    $('.afterpage').prepend($('#page-head-box').html());
    // 移动设备不支持 window.print()：隐藏打印按钮且不再自动唤起打印
    var isMobile=/Android|webOS|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);
    if(isMobile){
        var pb=document.querySelector('.btn-print');
        if(pb) pb.style.display='none';
    }else{
        window.onload = function(){
            setTimeout(function(){window.print(); }, 1000);
        }
    }
    // 手机端整页保存为图片
    function saveAsImage(){
        var tools=document.querySelector('.print-tools');
        tools.style.display='none';
        html2canvas(document.body,{backgroundColor:'#ffffff',scale:2}).then(function(canvas){
            tools.style.display='';
            var a=document.createElement('a');
            a.download='zitie-'+Date.now()+'.png';
            a.href=canvas.toDataURL('image/png');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }).catch(function(){
            tools.style.display='';
            alert('<?=$T[$lang]['saveFail'];?>');
        });
    }
</script>