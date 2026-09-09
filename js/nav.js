/**
 * 导航菜单：
 * 1. active 状态按当前路径自动判断（/ 与 /index.html 视为相同；/xiaoxue/ 下页面高亮"小学专区"）
 * 2. 移动端（≤768px）：左上角汉堡按钮，点击后菜单从左侧滑出
 */
(function () {
    'use strict';

    // 规范化路径：去掉末尾的 index.html 和末尾斜杠（/ 与 /index.html 视为相同）
    function norm(p) {
        return p.replace(/\/index\.html$/, '/').replace(/\/+$/, '');
    }

    var current = norm(window.location.pathname);
    var matched = false;
    var sectionLinks = [];

    // 当前语言（供抽屉切换按钮高亮）
    try { window.__curLang = window.localStorage.getItem('tzg-lang') || 'zh'; } catch (e) { window.__curLang = 'zh'; } // 专区入口链接（路径为一级目录，如 /xiaoxue、/waiyu）

    Array.prototype.forEach.call(document.querySelectorAll('.nav-bar a'), function (a) {
        a.classList.remove('active');
        var href = a.getAttribute('href');
        if (!href) return;
        var linkPath;
        try {
            linkPath = norm(new URL(href, window.location.href).pathname);
        } catch (e) {
            return;
        }
        if (/^\/[a-z]+$/.test(linkPath)) sectionLinks.push({ el: a, path: linkPath });
        if (linkPath === current) {
            a.classList.add('active');
            matched = true;
        }
    });

    // 专区子页面：无精确匹配时，高亮路径前缀匹配的专区入口
    if (!matched) {
        for (var i = 0; i < sectionLinks.length; i++) {
            if (current.indexOf(sectionLinks[i].path + '/') === 0) {
                sectionLinks[i].el.classList.add('active');
                break;
            }
        }
    }

    /* ============ 移动端抽屉菜单 ============ */

    var css = ''
        + '.menu-btn{ display:none; position:fixed; top:12px; left:12px; z-index:1001; width:40px; height:40px;'
        + ' border:none; border-radius:10px; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);'
        + ' color:#fff; font-size:20px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.2);'
        + ' align-items:center; justify-content:center; }'
        + '.menu-backdrop{ display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; }'
        + '.menu-backdrop.open{ display:block; }'
        + '.menu-drawer{ position:fixed; top:0; left:0; bottom:0; width:230px; background:#fff; z-index:1002;'
        + ' transform:translateX(-100%); transition:transform 0.25s ease;'
        + ' padding:60px 16px 16px; box-shadow:2px 0 12px rgba(0,0,0,0.15);'
        + ' overflow-y:auto; -webkit-overflow-scrolling:touch; }'
        + '.menu-drawer.open{ transform:translateX(0); }'
        + '.menu-drawer a{ display:block; padding:12px 14px; border-radius:10px; color:#2c3e50;'
        + ' text-decoration:none; font-size:16px; font-weight:600; }'
        + '.menu-drawer a.sub{ padding-left:34px; font-size:15px; font-weight:400; color:#555; display:none; }'
        + '.menu-drawer a.sub.show{ display:block; }'
        + '.menu-drawer a.has-sub::after{ content:"▾"; float:right; color:#999; }'
        + '.menu-drawer a.has-sub.open::after{ content:"▴"; }'
        + '.menu-drawer a.active{ background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; }'
        + '.site-lang-switcher{ display:flex; gap:6px; align-items:center; }'
        + '.site-lang-btn{ padding:8px 14px; font-size:13px; font-weight:600; border:1px solid rgba(255,255,255,0.5);'
        + ' border-radius:20px; background:transparent; color:#fff; cursor:pointer; transition:all .2s; }'
        + '.site-lang-btn.active, .site-lang-btn:hover{ background:#fff; color:#764ba2; }'
        + '.drawer-langs{ display:flex; gap:8px; margin-top:16px; padding-top:12px; border-top:1px solid #eee; }'
        + '.drawer-langs .site-lang-btn{ border-color:#ccc; color:#555; }'
        + '.drawer-langs .site-lang-btn.active{ background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; border-color:transparent; }'
        + '@media (max-width:768px){ .menu-btn{ display:flex; } .nav-bar{ display:none !important; } }';
    var style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'menu-btn';
    btn.setAttribute('aria-label', '菜单');
    btn.textContent = '☰';
    document.body.appendChild(btn);

    var backdrop = document.createElement('div');
    backdrop.className = 'menu-backdrop';
    document.body.appendChild(backdrop);

    var drawer = document.createElement('nav');
    drawer.className = 'menu-drawer';
    drawer.setAttribute('aria-label', '移动端菜单');
    document.body.appendChild(drawer);

    function openMenu() {
        // 打开时实时克隆导航链接（i18n 切换语言后内容也是最新的）
        // 子菜单默认收起，点击带 ▾ 的专区标题展开
        drawer.innerHTML = '';
        var bar = document.querySelector('.nav-bar');
        if (!bar) return;
        Array.prototype.forEach.call(bar.children, function (child) {
            if (child.tagName === 'A') {
                drawer.appendChild(child.cloneNode(true));
            } else if (child.classList && child.classList.contains('nav-dropdown')) {
                var parentA = child.querySelector('a');
                if (!parentA) return;
                var parent = parentA.cloneNode(true);
                parent.classList.add('has-sub');
                var subs = [];
                Array.prototype.forEach.call(child.querySelectorAll('.nav-sub a'), function (a) {
                    var c = a.cloneNode(true);
                    c.classList.add('sub');
                    subs.push(c);
                });
                parent.addEventListener('click', function (e) {
                    e.preventDefault();
                    var open = parent.classList.toggle('open');
                    subs.forEach(function (c) { c.classList.toggle('show', open); });
                });
                drawer.appendChild(parent);
                subs.forEach(function (c) { drawer.appendChild(c); });
            }
        });

        // 抽屉底部：语言切换按钮（移动端）
        var dlangs = document.createElement('div');
        dlangs.className = 'drawer-langs';
        [['zh', '简体'], ['zh-TW', '繁體'], ['en', 'EN']].forEach(function (l) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'site-lang-btn';
            b.textContent = l[1];
            if ((window.__curLang || 'zh') === l[0]) b.classList.add('active');
            b.addEventListener('click', function () { switchLang(l[0]); });
            dlangs.appendChild(b);
        });
        drawer.appendChild(dlangs);

        drawer.classList.add('open');
        backdrop.classList.add('open');
    }

    function closeMenu() {
        drawer.classList.remove('open');
        backdrop.classList.remove('open');
    }

    btn.addEventListener('click', openMenu);
    backdrop.addEventListener('click', closeMenu);
    drawer.addEventListener('click', function (e) {
        var a = e.target.closest('a');
        // 语言选项（含克隆来的）：切换语言
        if (a && a.getAttribute('data-lang')) {
            e.preventDefault();
            switchLang(a.getAttribute('data-lang'));
            return;
        }
        // 专区标题用于展开子菜单，不关闭抽屉；普通链接点击后关闭
        if (a && !a.classList.contains('has-sub')) closeMenu();
    });

    /* ============ 语言切换器（nav-bar 中的下拉菜单，样式同导航） ============ */

    function switchLang(lang) {
        try {
            window.localStorage.setItem('tzg-lang', lang);
            document.cookie = 'tzg_lang=' + lang + ';path=/;max-age=31536000;SameSite=Lax';
        } catch (e) {}
        var url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    }

    // 主站 index.html 自带 header 切换器，不重复注入
    var navBar = document.querySelector('.nav-bar');
    if (navBar && !document.querySelector('.lang-switcher')) {
        var LANGS = [['zh', '简体'], ['zh-TW', '繁體'], ['en', 'EN']];
        var cur = window.__curLang || 'zh';

        var dd = document.createElement('div');
        dd.className = 'nav-dropdown site-lang-dd';

        // 父项：显示"语言切换"，并记录当前语言
        var parent = document.createElement('a');
        parent.href = 'javascript:void(0)';
        parent.className = 'lang-parent';
        var curName = LANGS.find(function (l) { return l[0] === cur; });
        parent.textContent = '语言切换' + (curName ? '·' + curName[1] : '');
        dd.appendChild(parent);

        // 子菜单：简体/繁體/EN，当前语言加 active
        var sub = document.createElement('div');
        sub.className = 'nav-sub';
        LANGS.forEach(function (l) {
            var a = document.createElement('a');
            a.href = 'javascript:void(0)';
            a.textContent = l[1];
            a.setAttribute('data-lang', l[0]);
            if (cur === l[0]) a.classList.add('active');
            a.addEventListener('click', function () { switchLang(l[0]); });
            sub.appendChild(a);
        });
        dd.appendChild(sub);
        navBar.appendChild(dd);
    }
})();
