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
li svg{ margin-top:-11px; vertical-align:middle;}
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
.print-tools button:disabled{opacity:0.6;cursor:default;}
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
    // 手机端整页保存为图片（截图时按钮禁用置灰，并从图像中排除按钮组）
    function saveAsImage(){
        var btns=document.querySelectorAll('.print-tools button');
        btns.forEach(function(b){ b.disabled=true; });
        // 截图时调整字格内 SVG 的垂直位置
        var svgs=document.querySelectorAll('li svg');
        var oldMargins=[];
        svgs.forEach(function(s,i){ oldMargins[i]=s.style.marginTop; s.style.marginTop='-1px'; });
        function restore(){ btns.forEach(function(b){ b.disabled=false; }); svgs.forEach(function(s,i){ s.style.marginTop=oldMargins[i]; }); }
        function done(canvas){
            restore();
            var a=document.createElement('a');
            a.download='zitie-'+Date.now()+'.png';
            a.href=canvas.toDataURL('image/png');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        function fail(){ restore(); alert('<?=$T[$lang]['saveFail'];?>'); }
        if(isMobile){
            // 移动端直接用自绘渲染器（html2canvas 在部分 iOS 上因 SVG 污染 canvas 无法导出）
            try{ done(renderSheetToCanvas()); }catch(e){ fail(); }
            return;
        }
        var w=document.body.scrollWidth||document.documentElement.scrollWidth;
        var h=document.body.scrollHeight||document.documentElement.scrollHeight;
        var scale=Math.min(2,Math.sqrt(16000000/(w*h))||1);
        if(scale<1){ scale=1; }
        html2canvas(document.body,{backgroundColor:'#ffffff',scale:scale,useCORS:true,logging:false,
            ignoreElements:function(el){ return el.classList && el.classList.contains('print-tools'); }
        }).then(done).catch(function(){
            // html2canvas 失败（如 SVG 污染）时兜底用自绘渲染器
            try{ done(renderSheetToCanvas()); }catch(e){ fail(); }
        });
    }
    /*
     * 自绘渲染器：用 canvas 原生 API（线条/文字/Path2D 回放笔顺路径）重绘整页。
     * 不向画布绘制任何 SVG 图像，画布零污染，toDataURL 在 iOS Safari 也可导出。
     */
    function renderSheetToCanvas(){
        var scale=2;
        var W=Math.max(document.body.scrollWidth,document.documentElement.scrollWidth,940);
        var H=Math.max(document.body.scrollHeight,document.documentElement.scrollHeight);
        while(W*scale*(H*scale)>16000000 && scale>1){ scale-=0.25; }// iOS 画布上限保护
        if(scale<1){ scale=1; }
        var canvas=document.createElement('canvas');
        canvas.width=Math.round(W*scale);
        canvas.height=Math.round(H*scale);
        var ctx=canvas.getContext('2d');
        ctx.fillStyle='#ffffff';
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.scale(scale,scale);
        ctx.textAlign='center';
        ctx.textBaseline='middle';

        function rectOf(el){ var r=el.getBoundingClientRect(); return {x:r.left+window.scrollX,y:r.top+window.scrollY,w:r.width,h:r.height}; }
        function line(x1,y1,x2,y2,color,width,dash){
            ctx.save();
            ctx.strokeStyle=color; ctx.lineWidth=width||1; ctx.setLineDash(dash||[]);
            ctx.beginPath(); ctx.moveTo(x1,y1); ctx.lineTo(x2,y2); ctx.stroke();
            ctx.restore();
        }
        var GRID={'':{b:'#111',d:'#666'},'green':{b:'#00b050',d:'#70d39d'},'red':{b:'#980f29',d:'#bb3442'}};
        function drawGrid(r,type,colorKey){
            var c=GRID[colorKey]||GRID[''];
            if(type==='mzg'){
                line(r.x,r.y,r.x+r.w,r.y+r.h,c.d,1,[4,4]);
                line(r.x,r.y+r.h,r.x+r.w,r.y,c.d,1,[4,4]);
            }
            line(r.x,r.y+r.h/2,r.x+r.w,r.y+r.h/2,c.d,1,[4,4]);
            line(r.x+r.w/2,r.y,r.x+r.w/2,r.y+r.h,c.d,1,[4,4]);
            ctx.save(); ctx.strokeStyle=c.b; ctx.lineWidth=2; ctx.setLineDash([]);
            ctx.strokeRect(r.x+1,r.y+1,r.w-2,r.h-2); ctx.restore();
        }
        function drawText(text,r,font,color,dy){
            if(!text){ return; }
            ctx.save(); ctx.font=font; ctx.fillStyle=color;
            ctx.fillText(text,r.x+r.w/2,r.y+r.h/2+(dy||0));
            ctx.restore();
        }

        // 页头标题与班级/姓名/日期
        document.querySelectorAll('.page-head').forEach(function(el){ drawText(el.textContent,rectOf(el),'32px "楷体","Kaiti SC",KaiTi,serif','#666',12); });
        document.querySelectorAll('.page-info').forEach(function(el){ drawText(el.textContent,rectOf(el),'16px sans-serif','#666',0); });

        // 古诗标题/作者
        document.querySelectorAll('.gs-title').forEach(function(el){ drawText(el.textContent,rectOf(el),'bold 34px "楷体","Kaiti SC",KaiTi,serif','#333',0); });
        document.querySelectorAll('.gs-author').forEach(function(el){ drawText(el.textContent,rectOf(el),'20px "楷体","Kaiti SC",KaiTi,serif','#666',0); });

        // 字格与拼音格
        document.querySelectorAll('li').forEach(function(li){
            var r=rectOf(li);
            if(li.classList.contains('py')){
                line(r.x,r.y,r.x+r.w,r.y,'#999',1);
                line(r.x,r.y+r.h,r.x+r.w,r.y+r.h,'#999',2);
                line(r.x,r.y+15,r.x+r.w,r.y+15,'#bbb',1,[5,5]);
                line(r.x,r.y+31,r.x+r.w,r.y+31,'#bbb',1,[5,5]);
                drawText(li.textContent.replace(/ /g,'').trim(),r,'22px Arial,sans-serif','#555',4);
                return;
            }
            var bg=(getComputedStyle(li).backgroundImage||'');
            var m=bg.match(/(tzg|mzg)(green|red)?\.svg/);
            if(m){ drawGrid(r,m[1],m[2]||''); }
            var svg=li.querySelector('svg');
            if(svg){
                // 回放笔顺路径（与页面 g transform 一致：translate(-2.9,48) scale(0.058,-0.0572)）
                var sr=rectOf(svg);
                ctx.save();
                ctx.translate(sr.x,sr.y);
                ctx.translate(-2.9,48);
                ctx.scale(0.058,-0.0572);
                svg.querySelectorAll('path').forEach(function(p){
                    ctx.fillStyle=p.style.fill||'#000';
                    ctx.fill(new Path2D(p.getAttribute('d')));
                });
                ctx.restore();
            }else{
                drawText(li.textContent.replace(/ /g,'').trim(),r,'58px "楷体","Kaiti SC",KaiTi,serif',getComputedStyle(li).color,0);
            }
        });

        // 数学题
        document.querySelectorAll('.math-table td').forEach(function(td){
            drawText(td.textContent.trim(),rectOf(td),'30px "Times New Roman",Arial,sans-serif','#333',0);
        });

        // 英语四线三格
        document.querySelectorAll('.eng-grid').forEach(function(g){
            var r=rectOf(g);
            line(r.x,r.y,r.x+r.w,r.y,'#999',2);
            line(r.x,r.y+r.h,r.x+r.w,r.y+r.h,'#999',2);
            line(r.x,r.y+30,r.x+r.w,r.y+30,'#bbb',1,[5,5]);
            line(r.x,r.y+63,r.x+r.w,r.y+63,'#bbb',1,[5,5]);
        });
        document.querySelectorAll('.eng-text').forEach(function(el){
            var r=rectOf(el); ctx.save(); ctx.font='52px "Times New Roman",serif'; ctx.fillStyle='#c8c8c8'; ctx.textAlign='left';
            ctx.fillText(el.textContent,r.x,r.y+r.h/2); ctx.restore();
        });
        document.querySelectorAll('.eng-word-zh').forEach(function(el){
            var r=rectOf(el); ctx.save(); ctx.font='18px sans-serif'; ctx.fillStyle='#666'; ctx.textAlign='left'; ctx.fillText(el.textContent,r.x,r.y+r.h/2); ctx.restore();
        });
        document.querySelectorAll('.eng-zh').forEach(function(el){
            var r=rectOf(el); ctx.save(); ctx.font='20px sans-serif'; ctx.fillStyle='#888'; ctx.textAlign='left'; ctx.fillText(el.textContent,r.x,r.y+r.h/2+4); ctx.restore();
        });

        return canvas;
    }
</script>