# 烙馍网田字格字帖生成器

在线田字格/米字格字帖生成工具，支持笔顺、拼音，完全免费。

**在线地址**: [https://zt.luomor.com](https://zt.luomor.com)

## 功能特点

- **9574 个常用汉字** - 收录小学阶段常用汉字
- **田字格/米字格** - 两种主流练习格类型
- **笔顺显示** - 支持汉字笔顺动画
- **拼音标注** - 可选拼音显示
- **自定义内容** - 任意输入汉字生成
- **完全免费** - 在线生成，可直接打印

## 文件结构

```
/
├── index.html          # 主页 - 字帖生成器
├── scb.html            # 小学常用汉字生字表（一至六年级）
├── tzg.php             # 字帖生成后端处理
├── Pinyin.php          # 汉字转拼音工具类
├── bishun_data/        # 汉字笔顺数据（JSON 格式）
├── img/                # 图片资源（SVG 格子模板、截图）
├── robots.txt          # 搜索引擎爬虫配置
└── sitemap.xml         # 站点地图
```

## 使用说明

1. 访问 [index.html](index.html) 输入想要生成的汉字
2. 选择田字格/米字格、颜色、描红深浅等选项
3. 点击「立即生成」按钮
4. 页面自动打开打印对话框，勾选「打印背景」即可

## 生字表

[scb.html](scb.html) 页面收录了小学一至六年级常用汉字，共计 2513 个：

| 年级 | 汉字数量 |
|------|----------|
| 一年级 | 501 个 |
| 二年级 | 628 个 |
| 三年级 | 501 个 |
| 四年级 | 364 个 |
| 五年级 | 319 个 |
| 六年级 | 200 个 |

## 技术栈

- 纯静态 HTML/CSS/JavaScript
- PHP 后端处理（tzg.php）
- SVG 矢量图形（格子模板、笔顺路径）
- JSON 数据结构（笔顺数据）

## 部署

无需构建，直接部署到支持 PHP 的 Web 服务器即可。

```bash
# 本地测试（需要 PHP 环境）
php -S localhost:8000
```

# tianzigebishun
田字格笔顺生成
田字格笔顺 字帖生成器

基于开源项目: https://github.com/bunian/tianzigebishun 

添加了拼音功能 thanks for https://github.com/jifei/Pinyin

演示：
https://zt.luomor.com
