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
    var sectionLinks = []; // 专区入口链接（路径为一级目录，如 /xiaoxue、/waiyu）

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
        // 专区标题用于展开子菜单，不关闭抽屉；普通链接点击后关闭
        if (a && !a.classList.contains('has-sub')) closeMenu();
    });
})();
