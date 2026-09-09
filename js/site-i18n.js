/**
 * 专区页面多语言引擎（源文本字典方案）
 * - 语言解析：?lang= > localStorage(tzg-lang) > navigator.language，默认 zh（简体）
 * - 字典：lang/site-<code>.json，扁平 { "中文原文": "译文" }
 * - 实现：遍历文本节点/标题/meta/placeholder，命中字典即替换
 *   无需 data-i18n 标记；未命中保持中文原样
 * - 与 js/i18n.js（主站 index.html 专用）互不影响
 */
(function () {
    'use strict';

    var SUPPORTED = ['zh', 'zh-TW', 'en'];
    var DEFAULT = 'zh';

    function normalize(lang) {
        if (!lang) return null;
        var lower = String(lang).toLowerCase();
        for (var i = 0; i < SUPPORTED.length; i++) {
            if (SUPPORTED[i].toLowerCase() === lower) return SUPPORTED[i];
        }
        return null;
    }

    function detect() {
        var q = null;
        try { q = new URLSearchParams(window.location.search).get('lang'); } catch (e) {}
        q = normalize(q);
        if (q) return q;
        var saved = null;
        try { saved = window.localStorage.getItem('tzg-lang'); } catch (e) {}
        saved = normalize(saved);
        if (saved) return saved;
        var nav = (navigator.language || '').toLowerCase();
        if (/^zh[-_](tw|hk|mo|hant)/.test(nav)) return 'zh-TW';
        if (nav.indexOf('en') === 0) return 'en';
        return DEFAULT;
    }

    var lang = detect();
    if (lang === 'zh') return; // 简体为原文，无需处理

    // 字典路径：专区页面在子目录，主站页面在根目录
    var prefix = (location.pathname === '/' || location.pathname === '/index.html' || location.pathname === '/scb.html')
        ? 'lang/' : '../lang/';

    fetch(prefix + 'site-' + lang + '.json')
        .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(function (dict) { apply(dict); })
        .catch(function () { /* 字典加载失败保持中文 */ });

    function apply(dict) {
        document.documentElement.lang = lang === 'zh-TW' ? 'zh-TW' : 'en';

        function tr(text) {
            var key = text.replace(/\s+/g, ' ').trim();
            return dict[key] != null ? dict[key] : null;
        }

        // <title>
        var t = tr(document.title);
        if (t) document.title = t;

        // meta description/keywords
        ['description', 'keywords'].forEach(function (name) {
            var el = document.querySelector('meta[name="' + name + '"]');
            if (el) {
                var v = tr(el.getAttribute('content') || '');
                if (v) el.setAttribute('content', v);
            }
        });

        // 文本节点
        var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
        var nodes = [];
        var n;
        while ((n = walker.nextNode())) {
            var p = n.parentNode;
            if (!p) continue;
            var tag = p.nodeName;
            if (tag === 'SCRIPT' || tag === 'STYLE') continue;
            nodes.push(n);
        }
        nodes.forEach(function (node) {
            var v = tr(node.nodeValue);
            if (v) node.nodeValue = node.nodeValue.replace(/\S[\s\S]*\S|^\S$/, function (m) { return v; });
        });

        // placeholder
        Array.prototype.forEach.call(document.querySelectorAll('[placeholder]'), function (el) {
            var v = tr(el.getAttribute('placeholder'));
            if (v) el.setAttribute('placeholder', v);
        });
    }
})();
