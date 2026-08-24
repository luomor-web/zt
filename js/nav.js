/**
 * 导航菜单 active 状态：根据当前页面路径自动判断
 * 规则：链接路径与当前路径一致即高亮；位于 /xiaoxue/ 下的页面高亮"小学专区"
 */
(function () {
    'use strict';

    // 规范化路径：去掉末尾的 index.html 和末尾斜杠（/ 与 /index.html 视为相同）
    function norm(p) {
        return p.replace(/\/index\.html$/, '/').replace(/\/+$/, '');
    }

    var current = norm(window.location.pathname);
    var matched = false;
    var xxLink = null;

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
        if (linkPath === '/xiaoxue') xxLink = a;
        if (linkPath === current) {
            a.classList.add('active');
            matched = true;
        }
    });

    // 专区子页面：无精确匹配时高亮"小学专区"
    if (!matched && current.indexOf('/xiaoxue') === 0 && xxLink) {
        xxLink.classList.add('active');
    }
})();
