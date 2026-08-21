<?php
/* 小学练习专区共享函数库 */
include_once dirname(__FILE__).'/../Pinyin.php';

/* 主字体颜色（同 tzg.php） */
function xx_colors(){
	return ['green'=>'0,176,80','black'=>'0,0,0','red'=>'152,15,41'];
}

/* 描红辅助颜色（同 tzg.php，按 主色+深浅级别 取色） */
function xx_fz_colors(){
	return [
	'10'=>'255,255,255',
	'green1'=>'136,255,136','green2'=>'153,255,153','green3'=>'160,255,160','green4'=>'170,255,170','green5'=>'184,255,184','green6'=>'204,255,204',
	'black1'=>'136,136,136','black2'=>'153,153,153','black3'=>'160,160,160','black4'=>'170,170,170','black5'=>'184,184,184','black6'=>'204,204,204',
	'red1'=>'255,136,136','red2'=>'255,153,153','red3'=>'255,160,160','red4'=>'255,170,170','red5'=>'255,184,184','red6'=>'255,204,204',
	];
}

/* 楷体字体栈 */
function xx_kaiti(){
	return '"楷体","楷体_gb2312", "Kaiti SC", STKaiti, "AR PL UKai CN", "AR PL UKai HK", "AR PL UKai TW", "AR PL UKai TW MBE", "AR PL KaitiM GB", KaiTi, KaiTi_GB2312, DFKai-SB, "TW\-Kai"';
}

/* 打印页公共 CSS（格子背景需传相对子目录的路径，如 ../img/） */
function xx_sheet_css($bglx='tzg',$img_prefix='../img/'){
	$kaiti=xx_kaiti();
	return <<<CSS
body,div,p,ul,li{ padding:0; margin:0; list-style:none;}
body{ padding-top:60px; }/*屏幕显示时给顶部悬浮按钮留位*/
div{ width:938px; margin:0 auto;padding-left:2px; }
li{display: inline-block; width:80px; height:80px; font-family:{$kaiti}; font-size:58px; text-align:center; line-height:85px; background:url({$img_prefix}{$bglx}.svg); background-size:80px 80px; -webkit-print-color-adjust:exact; print-color-adjust:exact; margin:5px 0px 5px -2px; color:#b8b8b8; }
li.f{color:#000;margin-left:-0px}
li.svg{line-height:84px;}
li svg{ vertical-align:middle;}
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
CSS;
}

/* 拼音格行：一串汉字对应的拼音，每字一格（四线三格），补齐到 12 的整数倍 */
function xx_pinyin_row($text){
	preg_match_all('/./u',$text,$m);
	$chars=$m[0];
	$out='';
	foreach($chars as $c){
		$py=Pinyin::getPinyin($c);
		$out.='<li class="py">'.htmlspecialchars($py,ENT_QUOTES,'UTF-8').'</li>';
	}
	$n=count($chars);
	$pad=(12-($n%12))%12;
	for($i=0;$i<$pad;$i++){ $out.='<li class="py">&nbsp;</li>'; }
	return ['html'=>$out,'cells'=>$n+$pad];
}

/* 读取笔顺数据（GB2312 文件名回退），失败返回 null */
function xx_load_bishun($hz,$dir=null){
	if($dir===null){ $dir=dirname(__FILE__).'/../bishun_data/'; }
	$hzGBK=@iconv('UTF-8','GB2312',$hz);
	if($hzGBK && file_exists($dir.$hzGBK.'.json')){
		$json=file_get_contents($dir.$hzGBK.'.json');
	}elseif(file_exists($dir.$hz.'.json')){
		$json=file_get_contents($dir.$hz.'.json');
	}else{
		return null;
	}
	$data=json_decode($json,1);
	return is_array($data)&&isset($data['strokes']) ? $data : null;
}

/* 完整字 SVG（主色） */
function xx_char_svg($data,$color){
	$html='<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
	foreach($data['strokes'] as $v){
		$html.='<path d="'.$v.'" style="fill:rgb('.$color.');stroke:rgb('.$color.');" stroke-width="0"></path>';
	}
	return $html.'</g></svg></li>';
}

/*
 * 渲染一个汉字的整行字帖格（复刻 tzg.php 模式）：
 * 完整字 1 格 + 逐笔描红 N 格 + 补满 12 的整数倍（bs=1 填完整字描红，bs=0 填空格）
 * 返回占用的格子数；无笔顺数据时输出 12 个空格并返回 12
 */
function xx_render_hanzi_row($hz,$color,$fcolor,$bs){
	$data=xx_load_bishun($hz);
	if(!$data){
		$out='';
		for($i=0;$i<12;$i++){ $out.='<li>&nbsp;</li>'; }
		return ['html'=>$out,'cells'=>12];
	}
	$count=count($data['strokes']);
	$out=xx_char_svg($data,$color);
	// 按笔数逐笔描红
	for($i=0;$i<$count;$i++){
		$out.='<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
		for($ii=0;$ii<=$i;$ii++){
			$out.='<path d="'.$data['strokes'][$ii].'" style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width="0"></path>';
		}
		$out.='</g></svg></li>';
	}
	// 补满 12 的整数倍
	$tzg12=($count+1)/12;
	$kg=0;
	if(!is_int($tzg12)){
		$kg=12-(12*$tzg12);
	}
	if($kg<0){
		$kg=((ceil(abs($kg)/12)+1)*12)-($count+1);
	}
	if($kg && $bs){
		for($i=0;$i<$kg;$i++){
			$out.='<li class="svg"><svg width="54" height="54" style="margin-top: -11px;"><g transform="translate(-2.9,48) scale(0.058, -0.0572)">';
			foreach($data['strokes'] as $v){
				$out.='<path d="'.$v.'" style="fill:rgb('.$fcolor.');stroke:rgb('.$fcolor.');" stroke-width="0"></path>';
			}
			$out.='</g></svg></li>';
		}
	}
	if($kg && !$bs){
		for($i=0;$i<$kg;$i++){
			$out.='<li class="svg">&nbsp;</li>';
		}
	}
	return ['html'=>$out,'cells'=>$count+1+(int)$kg];
}

/* 描红文字行：一串汉字以浅灰显示在格子上（组词/古诗用），补齐到 12 的整数倍 */
function xx_trace_text_row($text,$bglx_color='#c8c8c8'){
	preg_match_all('/./u',$text,$m);
	$chars=$m[0];
	$out='';
	foreach($chars as $c){
		$out.='<li style="color:'.$bglx_color.'">'.htmlspecialchars($c,ENT_QUOTES,'UTF-8').'</li>';
	}
	$n=count($chars);
	$pad=(12-($n%12))%12;
	for($i=0;$i<$pad;$i++){ $out.='<li>&nbsp;</li>'; }
	return ['html'=>$out,'cells'=>$n+$pad];
}

/* 空白格行 */
function xx_blank_row($rows=1){
	$out='';
	for($i=0;$i<12*$rows;$i++){ $out.='<li>&nbsp;</li>'; }
	return ['html'=>$out,'cells'=>12*$rows];
}

/* 打印页尾部：页头注入 + 自动打印（同 tzg.php）；$info 为班级/姓名/日期信息行 */
function xx_auto_print($title,$info=''){
	$title=htmlspecialchars($title,ENT_QUOTES,'UTF-8');
	return <<<HTML
<div class="print-tools">
<button type="button" class="btn-print" onclick="window.print()">🖨️ 打印</button>
<button type="button" class="btn-save" onclick="saveAsImage()">💾 保存图片</button>
<button type="button" class="btn-close" onclick="window.close()">✕ 关闭</button>
</div>
<div id="page-head-box" style="display: none;">
<div class="page-head">{$title}</div>
<div class="page-info">{$info}</div>
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
        function fail(){ restore(); alert('保存失败，请截屏保存'); }
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
HTML;
}

/* 班级/姓名/日期信息行（小学专区仅中文；空值显示下划线供手写，日期默认不填写） */
function xx_student_info(){
	$class=trim($_POST['class']??'');
	$name=trim($_POST['name']??'');
	$c=$class!==''?htmlspecialchars($class,ENT_QUOTES,'UTF-8'):'＿＿＿＿';
	$n=$name!==''?htmlspecialchars($name,ENT_QUOTES,'UTF-8'):'＿＿＿＿';
	return '班级：'.$c.'　姓名：'.$n.'　日期：＿＿＿＿';
}

/* 每 15 行分页：传入累计格数，需要分页时输出分页标记 */
function xx_page_break(&$used_cells){
	$rows=$used_cells/12;
	$pages=$rows/15;
	if(is_int($pages)){
		$used_cells=0;
		return "</ul></div><div class='afterpage'><ul>";
	}
	return '';
}

/* 过滤只保留中文 */
function xx_filter_hanzi($words){
	preg_match_all('/[\x{4e00}-\x{9fff}]+/u',$words,$m);
	return implode('',$m[0]);
}

/* 英语四线三格 CSS（用边框画线，打印不依赖背景图形设置） */
function xx_eng_css(){
	return <<<CSS
.eng-line{ width:938px; margin:0 auto; }
.eng-grid{ position:relative; height:96px; border-top:2px solid #999; border-bottom:2px solid #999; margin-bottom:60px;
	-webkit-print-color-adjust:exact; print-color-adjust:exact; }
.eng-grid::before{ content:""; position:absolute; left:0; right:0; top:30px; border-top:1px dashed #bbb; }
.eng-grid::after{ content:""; position:absolute; left:0; right:0; top:63px; border-top:1px dashed #bbb; }
.eng-text{ font-family:"Times New Roman",Georgia,serif; font-size:52px; color:#c8c8c8; padding-left:30px; line-height:96px; letter-spacing:2px; }
.eng-zh{ font-family:sans-serif; font-size:20px; color:#888; padding:2px 0 10px 30px; }
CSS;
}

/* 打印页 head（生成结果页用） */
function xx_sheet_head($title,$extra_css='',$bglx='tzg'){
	$t=htmlspecialchars($title,ENT_QUOTES,'UTF-8');
	return "<!DOCTYPE html>\n<html lang=\"zh-CN\">\n<head>\n<meta charset=\"utf-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<title>{$t}</title>\n<style>\n".xx_sheet_css($bglx).$extra_css."\n</style>\n</head>\n<body>\n";
}
