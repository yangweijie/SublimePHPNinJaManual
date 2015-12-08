# SublimePHPNinJaManual
Sublime中的函数手册提示，中文，其他语言的可以通过命令生成

## 由来
自己因为sublime中没有一个好的php函数提示所苦恼。曾经在Sublime的thinkphp插件里实现过一次，![](https://camo.githubusercontent.com/759b876842251b13854ff35267cc005b39d0ddb8/687474703a2f2f7777772e7468696e6b7068702e636e2f55706c6f6164732f7370656563682f323031332d30382d30342f353166653061613430636134302e706e67) ![](https://camo.githubusercontent.com/4749d5a82cd9e4cba79622833f3e0c7699bf5ba7/687474703a2f2f7777772e7468696e6b7068702e636e2f55706c6f6164732f7370656563682f323031332d30382d30342f353166653062646361333036632e706e67) 那个时候用的是netbeans 里 php提示的文件库，然后显示也不大好看。一直以为sublime 没法做到好看的ui 因为api少。

前几天发现了国人的自己做的[Ctranslator tool](https://packagecontrol.io/packages/Ctranslator%20tool) ![](https://packagecontrol.io/readmes/img/927f757b88e0448227a2db8f9e312a9686eea391.gif) 这不就是我想要的吗？看源码发现是用了一个开源的库 [StyledPopup](https://github.com/huot25/StyledPopup)。 用html灵活多了。

而后，自己其实一直用的chrome 浏览器的插件，PHP NanJa Manual。支持各种语言，也有示列，就是每次写代码开浏览器太麻烦了。由于他提供一个开源库[PHP doc parser](https://github.com/martinsik/php-doc-parser)，可以将php官方手册转换成json文件，自己就有了移植的想法。
![](https://raw.githubusercontent.com/martinsik/php-doc-parser/master/doc/animation.gif)
所以名字就参考了他的，希望不要告侵权。
由于son文件过于大，python没有缓存机制（或许我不知道），我就用thinkphp 转成了一个sqlite db。2个表 fun、funlist  fun存 函数名， funlist存 函数名和对应son数据。
## 安装
使用Sublime Text 3 Package Control 插件(http://wbond.net/sublime\_packages/package\_control) 按 CTRL + SHIFT + P 后 找到 _Package Control: Install Package_ 然后回车。列表中找到**PhpNinJaManual**这个插件（等审核过了会有）。

或者直接 git clone 到你的 Sublime Text 3 packages 目录 (usually located at /Sublime Text 3/Packages/)。记得把SublimePHPNinJaManual 改为PhpNinJaManual。pac 安装的应该没这个问题。

## 使用说明
选中要查看的php函数名，然后右键会发现 “查看函数说明”菜单![](http://ww3.sinaimg.cn/mw1024/50075709gw1eweqa43989j20c40e077h.jpg) ，点击后，
会弹出函数说明浮层 ![](http://ww2.sinaimg.cn/mw1024/50075709gw1ewfznodyolj20mt07sq5t.jpg)

如果想配快捷键，只需要你自定义快捷键里 commond 写 `show_php_document`就行了，这个参考Sublime手册快捷键，自己定义一个就好了。比如 f1或者 其他。
## 关于手册其他语言的生成
拿英文 en 举例。
先到 手册解析器主页：https://github.com/martinsik/php-doc-parser
找一个目录 写上composer.json 
内容：

	{
		"require": {
		  ...
		  "martinsik/php-doc-parser": "~2.0"
		}
	}

然后 `composer.phar install` 也可能 composer install
装好后， 当前目录vendor/bin 下会有![](http://ww3.sinaimg.cn/mw1024/50075709gw1eweqbqozgtj208c03w0sq.jpg) 执行文件，然后 

`vendor/bin/doc-parser help parser:run`
![](https://raw.githubusercontent.com/martinsik/php-doc-parser/master/doc/animation.gif)
生成好这2个json文件后， 复制到，插件目录的 App/Runtime/Data里，![](http://ww1.sinaimg.cn/mw1024/50075709gw1eweqeagv1bj208503dglk.jpg) 

到时候就不是zh 而是en。
然后 命令行切换到插件目录里执行 2条命令 ：
- `php index.php "Doc/importFun/lang/en"`
- `php index.php "Doc/importFunList/lang/en"`
![](http://ww3.sinaimg.cn/mw1024/50075709jw1eysahlch8hj20b501jwf2.jpg)
会提示多少函数导入了。这是最新中文版的数量，en的是9863 个函数

PS：因为怕用户麻烦，我更新了中文的数据库文件和添加了英文数据库文件，英文的比较全多大概300个函数。如果想使用英文的同学 git clone最新版的插件，修改插件配置 lang->en 保存后重启插件。
就能看到如下的图：![en doc support](http://ww1.sinaimg.cn/mw1024/50075709jw1eysanj3tdij20k007aaca.jpg)
## 未来特性
可能会用PHPConnector 重构下。
可能会把示列加上，不过数据库体积就更大了，而且可能显示会更长
## 注意点
- <s>有的函数因为返回了&$count 这类Sublime插件语言中的关键字导致解析不了向后的html字符串![](http://ww1.sinaimg.cn/mw1024/50075709gw1eweqfm5h0tj20az04a0to.jpg)。暂时不知道怎么修复</s> 已经被作者告知了方法，已修复。PHP返回那边转义了'&'符号 √
- <s>由于那个浮层组件不支持设定宽高，目前长内容会出现滚动条，只能等作者解决了，我会向他提Issue的。</s> 作者也告知了有个宽度的参数 ，我改了下插件，设了700 以后提供配置吧。反正我用str_replace函数测试足够了。√
- 解析手册用的是PHP，需要你们的 命令行里php 可用，所有， 最好检查下自己的系统环境变量或者聪明的里 php -v 能不能用
- 参数解释前添加了 返回类型显示和高亮，另外函数名可以点击 去PHP官网查看。
- 如果你想定义输出的格式，和样式可以去看插件目录里的app Doc控制器的find方法。和find.html 模板。
- 有时候会报以下错误，![](http://ww1.sinaimg.cn/large/50075709jw1ex8myfz0r6j20uj031tam.jpg) 这个错误是我插件的默认配置在插件初始化时没读取到，导致lang 变量不是个对象。解决方法是：将插件的默认配置，去菜单->参数->PhpNinJaManual 里 ![](http://ww1.sinaimg.cn/mw1024/50075709gw1ewy9zjlg1aj20n50feacx.jpg) 里将默认配置复制后找到用户配置 粘贴保存后重新尝试即可。

find.html

~~~html
<style>
a{
	color: #62D9EF;
}
</style>
<span class="keyword">{$fun.params.0.ret_type}</span> <a class="entity name function" href="{$url}">{$fun.params.0.name}</a> (<span class="comment line"><volist name="fun.params.0.list" id="i">
	[ <neq name="key" value="0">,</neq><span class="keyword">{$i.type}</span> <span class="string quoted">{$i.var}</span> ]' {$i['beh']? $i['beh']:$i['type']}  {$i.var}
</volist></span>)
<p>{$fun.long_desc}</p>
<p>参数: <br>
<volist name="fun.params.0.list" id="i">
	{$i.var} - {$i['desc']? $i['desc']: '暂无说明'} <br>
	</volist>
</p>
~~~
直接改HTML和控制器后直接调试插件的效果太他妈爽了，可惜我没有配色天赋。曾经尝试body白色，想弄个清淡的浮层，发现body外还有边距。反正大家可以自由DIY。配出适合自己主题的样式。可以告诉我，我以后，可以动态的针对不同主题调用不同样式，达到显示最优化。
- 今天发现有人查不出来，因为php命令行里没有开启sqlite扩展😊，于是帮他的iis中php开启了保险起见也开启了pdo_sqlite。大家下次记得先php -m 看看命令行的php能不能用sqlite。
## 有问题反馈
在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

* 邮件(yangweijiest#gmail.com, 把#换成@)
* QQ: 917647288
* weibo: [@黑白世界4648](http://weibo.com/1342658313)
* 人人: [@杨维杰](http://www.renren.com/247050624)
## 关于作者

	var code-tech = {
	    nickName  : "杨维杰",
	    site : "http://code-tech.diandian.com"
	}
