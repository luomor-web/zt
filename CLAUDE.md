# CLAUDE.md

本文档为 Claude Code (claude.ai/code) 在此代码库中工作提供指导。

## 项目概述

烙馍网田字格字帖生成器 - 一个可生成带笔顺和拼音的汉字练习字帖（田字格/米字格）的 Web 应用程序。

**核心功能：**
- 支持 9574 个常用汉字
- 生成田字格或米字格练习纸
- 使用 SVG 路径显示笔顺动画
- 可选拼音显示
- 自定义格子、文字、描红颜色
- 页面加载后自动打印

## 项目结构

```
/
├── index.html      # 主页面，包含表单 UI
├── tzg.php         # 后端处理器 - 生成可打印字帖
├── scb.html        # 小学分级常用汉字生字表参考
├── Pinyin.php      # 汉字转拼音工具类
├── favicon.ico     # 网站图标
├── js/
│   └── i18n.js     # 多语言引擎（见下文「多语言 (i18n)」）
├── lang/           # 语言包（扁平 dotted key JSON）
│   ├── zh.json     # 简体中文（默认，key 的权威清单）
│   ├── zh-TW.json  # 繁体中文
│   └── en.json     # 英文
├── img/            # SVG 格子模板和截图
│   ├── tzg.svg, tzggreen.svg, tzgred.svg    # 田字格
│   ├── mzg.svg, mzggreen.svg, mzgred.svg    # 米字格
│   └── print.png, xg*.png, zt1.jpg          # 截图
├── xiaoxue/        # 小学练习专区（仅中文，不接入 i18n）
│   ├── index.html  # 入口导航页（4 个功能卡片，纯静态）
│   ├── form.css    # 表单页公共样式
│   ├── lib.php     # 共享函数：打印 CSS、笔顺格渲染、四线三格
│   ├── zuci.html/.php   # 组词字帖（.html 静态表单 → POST .php 生成）
│   ├── gushi.html/.php  # 古诗练习（诗单由 JS fetch ../data/gushi.json 渲染）
│   ├── math.html/.php   # 数学题（无数据文件，随机出题）
│   └── english.html/.php # 英语练习（字母/单词/句子）
├── data/           # 练习数据（手工整理的权威内容，可整体替换刷新）
│   ├── zuci.json              # 组词：{"一":["一个","一天","第一"],...} 约 600 字
│   ├── gushi.json             # 课标必背古诗 75 首：[{title,author,dynasty,content:[行]}]
│   ├── english_words.json     # 小学课标单词约 220 个：[{en,zh,grade:1-4}]
│   └── english_sentences.json # 常见句型约 63 句：[{en,zh}]
└── bishun_data/    # 约 47MB 的笔顺数据 JSON 文件
    ├── 一.json, 二.json, ... (单字文件)
    └── ⺀.json, ⺈.json, ... (部首文件)
```

## 数据格式

笔顺数据文件 (`bishun_data/*.json`) 结构：
```json
{
  "strokes": ["M 323 706 Q 325 699...", ...],
  "medians": [[[x, y], ...], ...]
}
```
- `strokes`: 每笔的 SVG 路径命令数组
- `medians`: 中线坐标数组

字符文件支持 UTF-8 和 GB2312 两种编码文件名（`tzg.php:103-107` 有回退逻辑）。

## 核心组件

### index.html
表单式 UI，用户可：
- 输入汉字（自动过滤非中文字符）
- 选择格子类型（田字格/米字格）
- 选择颜色（格子/文字绿/黑/红）
- 设置描红深浅（6 级）
- 开关拼音显示和笔顺填充

### tzg.php
服务端处理器：
1. 仅保留中文字符（正则 `[\x{4e00}-\x{9fff}]+`）
2. 生成带 SVG 笔顺路径的 HTML
3. 通过 JS 自动触发打印对话框
4. 处理分页（每页 15 行，每行 12 格）

### Pinyin.php
包含静态方法 `Pinyin::getPinyin($char)` 返回**带声调符号**的拼音（如 `hàn`，轻声不标调）。字典数据来自 mozillazg/pinyin-data，已按本站原有多音字取舍匹配（如 秘→mì, 迫→pò）。

## 开发说明

- **无构建系统** - 纯 PHP/HTML/CSS/SVG，部署即用
- **需要 PHP** - 服务器需支持 PHP 运行 `tzg.php`
- **字符编码** - 文件使用 UTF-8；笔顺数据查找支持 GB2312 回退
- **打印样式** - CSS `@page` 设置 5mm/16mm 边距；`page-break-before` 处理分页
- **浏览器兼容** - 面向 Edge/Chrome；需开启"打印背景"选项

## 表单参数（POST 到 tzg.php）

| 参数 | 取值 | 说明 |
|------|------|------|
| `words` | 文本 | 要生成的汉字 |
| `types` | tzg/mzg | 格子类型 |
| `bgcolor` | green/black/red | 格子线颜色 |
| `zcolor` | green/black/red | 主字颜色 |
| `fcolor` | 1-6 | 描红深浅级别 |
| `bs` | 0/1 | 笔顺填充 |
| `py` | 0/1 | 显示拼音 |
| `title` | 文本 | 自定义页头 |

## 多语言 (i18n)

- **前端**：`js/i18n.js` + 元素属性标记，覆盖 `index.html`；`scb.html` 未接入
- **语言包**：`lang/<code>.json`，扁平 dotted key（如 `options.fcolor.1`），`zh.json` 是 key 的权威清单
- **属性约定**：
  - `data-i18n="key"` → textContent
  - `data-i18n-html="key"` → innerHTML（值含内联标签，需与 CSS 选择器保持一致）
  - `data-i18n-attr="attr:key;attr:key"` → setAttribute（placeholder/aria-label/alt）
  - `data-i18n-value="key"` → `.value`，仅在用户未编辑过该字段时生效（脏字段保护）
- **语言解析顺序**：`?lang=` 参数 > `localStorage('tzg-lang')` > `navigator.language`，默认 `zh`
- **持久化**：手动切换写 localStorage + cookie `tzg_lang`（path=/，1 年）
- **传给后端**：表单隐藏字段 `name="lang"`（JS 填充），cookie `tzg_lang` 兜底；`tzg.php` 用内嵌 `$T` 数组翻译 `<title>` / `<html lang>` / 空页头默认值
- **兜底**：HTML 中硬编码的中文即兜底内容，语言包加载失败页面仍完整可用
- **新增语言**：新建 `lang/<code>.json` → `i18n.js` 的 `SUPPORTED` 加语言码 → 切换器加按钮 → `tzg.php` 的 `$T` 加一项

## 小学练习专区 (xiaoxue/)

- **模式**：与主站一致——`.html` 纯静态表单页（target=_blank POST）→ `.php` 生成打印页并自动 `window.print()`；PHP 仅在需要动态数据时使用
- **共享函数**（`xiaoxue/lib.php`）：
  - `xx_sheet_head()/xx_sheet_css()` — 打印页骨架（格子背景相对路径 `../img/`）
  - `xx_render_hanzi_row()` — 单字笔顺描红整行（复刻 tzg.php 逻辑，补满 12 格）
  - `xx_trace_text_row()/xx_blank_row()` — 描红文字行 / 空白格行
  - `xx_page_break()` — 每 15 行分页
  - `xx_eng_css()` — 英语四线三格（linear-gradient 背景）
- **数据**：`data/*.json` 为手工整理内容；gushi.html 诗单由前端 fetch JSON 渲染，数据变更无需改代码
- **注意**：子目录引用 `bishun_data/` 与 `img/` 需 `../` 前缀（lib.php 已处理）

## 外部依赖

- jQuery 2.1.1 (CDN) - 打印前 DOM 操作
- 百度统计脚本
- `img/` 目录中的 SVG 格子模板
