/**
 * 烙馍网田字格字帖生成器 - 多语言 (i18n) 引擎
 *
 * 语言解析顺序：?lang= 参数 > localStorage(tzg-lang) > navigator.language，默认 zh
 * HTML 中硬编码的中文即兜底内容，语言包加载失败不影响页面使用。
 *
 * 元素属性约定：
 *   data-i18n="key"                    → textContent
 *   data-i18n-html="key"               → innerHTML（翻译值含内联标签）
 *   data-i18n-attr="attr:key;attr:key" → setAttribute（placeholder/aria-label/alt 等）
 *   data-i18n-value="key"              → .value（仅当用户未编辑过该字段时）
 *
 * 新增语言步骤：
 *   1. 新建 lang/<code>.json（key 与 zh.json 一致）
 *   2. 下方 SUPPORTED 数组加入 '<code>'
 *   3. index.html 语言切换器加一个 .lang-btn 按钮（data-lang="<code>"）
 *   4. tzg.php 的 $T 数组加 '<code>' 项
 */
(function () {
    'use strict';

    var SUPPORTED = ['zh', 'en'];
    var DEFAULT = 'zh';
    var LS_KEY = 'tzg-lang';
    var COOKIE_KEY = 'tzg_lang';

    var cache = {};      // lang -> dict
    var current = DEFAULT;

    function isSupported(lang) {
        return SUPPORTED.indexOf(lang) > -1;
    }

    function detect() {
        var q = null;
        try {
            q = new URLSearchParams(window.location.search).get('lang');
        } catch (e) { /* 忽略老浏览器 */ }
        if (isSupported(q)) return q;

        var saved = null;
        try {
            saved = window.localStorage.getItem(LS_KEY);
        } catch (e) { /* 隐私模式等场景忽略 */ }
        if (isSupported(saved)) return saved;

        var nav = (navigator.language || navigator.userLanguage || '').toLowerCase();
        return nav.indexOf('en') === 0 ? 'en' : DEFAULT;
    }

    function persist(lang) {
        try {
            window.localStorage.setItem(LS_KEY, lang);
        } catch (e) { /* 忽略 */ }
        // cookie 作为 tzg.php 的兜底（表单隐藏字段是主通道）
        document.cookie = COOKIE_KEY + '=' + lang + ';path=/;max-age=31536000;SameSite=Lax';
    }

    function load(lang) {
        if (cache[lang]) return Promise.resolve(cache[lang]);
        return fetch('lang/' + lang + '.json')
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (dict) {
                cache[lang] = dict;
                return dict;
            });
    }

    function t(dict, key) {
        return dict && dict[key] != null ? dict[key] : null;
    }

    function setMeta(attr, name, value) {
        if (value == null) return;
        var el = document.querySelector('meta[' + attr + '="' + name + '"]');
        if (el) el.setAttribute('content', value);
    }

    function applyDict(dict) {
        // <title> 与 meta 标签
        if (t(dict, 'meta.title')) document.title = t(dict, 'meta.title');
        setMeta('name', 'title', t(dict, 'meta.title'));
        setMeta('name', 'keywords', t(dict, 'meta.keywords'));
        setMeta('name', 'description', t(dict, 'meta.description'));
        setMeta('property', 'og:title', t(dict, 'meta.ogTitle'));
        setMeta('property', 'og:description', t(dict, 'meta.ogDescription'));
        setMeta('name', 'twitter:title', t(dict, 'meta.twitterTitle'));
        setMeta('name', 'twitter:description', t(dict, 'meta.twitterDescription'));
        document.documentElement.lang = current === 'zh' ? 'zh-CN' : current;

        // 文本内容
        Array.prototype.forEach.call(document.querySelectorAll('[data-i18n]'), function (el) {
            var v = t(dict, el.getAttribute('data-i18n'));
            if (v != null) el.textContent = v;
        });

        // HTML 内容（翻译值含 <strong>/<a> 等内联标签）
        Array.prototype.forEach.call(document.querySelectorAll('[data-i18n-html]'), function (el) {
            var v = t(dict, el.getAttribute('data-i18n-html'));
            if (v != null) el.innerHTML = v;
        });

        // 属性（placeholder、aria-label、alt 等）
        Array.prototype.forEach.call(document.querySelectorAll('[data-i18n-attr]'), function (el) {
            el.getAttribute('data-i18n-attr').split(';').forEach(function (pair) {
                var idx = pair.indexOf(':');
                if (idx < 0) return;
                var attr = pair.slice(0, idx).trim();
                var v = t(dict, pair.slice(idx + 1).trim());
                if (v != null) el.setAttribute(attr, v);
            });
        });

        // 表单默认值（用户编辑过后不再覆盖）
        Array.prototype.forEach.call(document.querySelectorAll('[data-i18n-value]'), function (el) {
            var v = t(dict, el.getAttribute('data-i18n-value'));
            if (v != null && !el.dataset.i18nDirty) el.value = v;
        });

        // 隐藏 lang 字段（随表单 POST 给 tzg.php）
        var langField = document.getElementById('lang-field');
        if (langField) langField.value = current;

        // 切换按钮 active 状态
        Array.prototype.forEach.call(document.querySelectorAll('.lang-btn'), function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === current);
        });
    }

    function setLang(lang, save) {
        if (!isSupported(lang)) lang = DEFAULT;
        current = lang;
        if (save) persist(lang);
        load(lang).then(applyDict).catch(function () {
            // 语言包加载失败：保持 HTML 中硬编码的中文兜底内容
        });
    }

    // 用户编辑过的字段打脏标记，切换语言时不覆盖其值
    Array.prototype.forEach.call(document.querySelectorAll('[data-i18n-value]'), function (el) {
        el.addEventListener('input', function () {
            el.dataset.i18nDirty = '1';
        }, { once: true });
    });

    // 语言切换按钮
    Array.prototype.forEach.call(document.querySelectorAll('.lang-btn'), function (btn) {
        btn.addEventListener('click', function () {
            setLang(btn.getAttribute('data-lang'), true);
        });
    });

    var initial = detect();
    // URL 参数显式指定语言时也持久化，便于分享链接
    try {
        if (new URLSearchParams(window.location.search).get('lang')) persist(initial);
    } catch (e) { /* 忽略 */ }
    setLang(initial, false);

    // 暴露给外部（将来扩展用）
    window.i18nSetLang = function (lang) { setLang(lang, true); };
    window.i18nGetLang = function () { return current; };
})();
