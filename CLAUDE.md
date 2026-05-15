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
├── img/            # SVG 格子模板和截图
│   ├── tzg.svg, tzggreen.svg, tzgred.svg    # 田字格
│   ├── mzg.svg, mzggreen.svg, mzgred.svg    # 米字格
│   └── print.png, xg*.png, zt1.jpg          # 截图
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
包含静态方法 `Pinyin::getPinyin($char)` 返回拼音字符串。含多音字映射（如 秘→mi, 迫→po）。

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

## 外部依赖

- jQuery 2.1.1 (CDN) - 打印前 DOM 操作
- 百度统计脚本
- `img/` 目录中的 SVG 格子模板
