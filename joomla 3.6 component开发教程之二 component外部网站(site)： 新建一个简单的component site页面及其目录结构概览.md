#新建一个简单的component前台（site）页面
##joomla中component的安装方式主要分为三种：
* 1--上传zip包
![uploda-zip](http://img.my.csdn.net/uploads/201611/13/1479013401_7381.png)
* 2--指定服务器本地目录（目录结构需与zip包中的结构一致） 

<pre>
    com_publish/  
    ├── <b>admin</b>  
    ├── <b>publish.xml</b> 
    └── <b>site</b> 
        ├── publish.php  
        └── views  
            └── publish  
                ├── tmpl  
                │   └── default.php  
                └── view.html.php            
</pre>
* 3--通过管理后台自动发现
![dir-install](http://img.my.csdn.net/uploads/201611/13/1479011950_5215.png)

如果一切顺利，安装完毕后直接输入<code>你的域名/index.php?option=com_publish</code>，应该就能看到Hello World了。
##component部分的目录结构概览（即zip包中的目录结构）
大家大概已经注意到，插件安装完成后，不同的目录和文件都安装到joomla项目下不同的地方去了，相对位置和在zip包内的相比发生了变化，现在我们一个个把他们找出来：
###publish.xml文件
此文件为component配置文件，每个component都有自己的配置文件，其中包含了该component的基本信息（如：作者联系方式、使用条款等）以及相关文件在zip包中的相对位置；

该文件在安装之后会被复制到<code>/opt/joomla-tutorial/administrator/components/com_publish/</code>目录下（见下文）
###admin目录
<pre>
admin/
└── controllers
</pre>
目前我们还没有涉及到admin后台，因此就放了一个controller目录，这个目录很明显是放controller相关代码的，后面admin相关章节会讲到，这里只是一个空壳，为了演示用。

如教程一所说，一个component安装完成后，admin部分的路径为 <code>joomla根目录/administrator/com_XXX/</code>，因此除了publish.xml文件之外，zip包中admin目录下的所有内容都会被复制到这个目录。

插件安装好之后，博主服务器上的目录结构如下：  $controller->execute($input->getCmd('task'));
<pre>
/opt/joomla-tutorial/administrator/components/com_publish/
├── controllers
└── publish.xml
</pre>
###site目录
site部分的路径为 <code>joomla根目录/components/com_XXX/</code>，即：zip包中site目录下的所有内容都会被复制到这个目录。
<pre>
/opt/joomla-tutorial/components/com_publish
├── controller.php
├── controllers
├── index.html
├── models
├── publish.php
└── views
    └── publish
        ├── tmpl
        │   └── default.php
        └── view.html.php
</pre>
##site目录初探
从上文中的目录概览中我们可以看出，除了配置文件之外，一个component包含两大部分（见教程一）
###component入口：publish.php
每个component都有且只有一个入口，在本教程中，这个文件的名字就是<code>publish.php</code>，要注意的是：名字不能错，不然joomla程序就找不到该component的位置了，我们为它写的所有逻辑都无法得到体现；

我们来看下这个入口文件的内容：
<pre><code>
&lt?php&gt
/**
 *@author diphuaji
 */
// 检查全局常量 _JEXEC是否存在，不存在则退出
defined('_JEXEC') or die('Restricted access');
// 获取一个 controller，如上节所说，Controller算是MVC的入口
$controller = JControllerLegacy::getInstance('Publish'); 
// 获取input，所谓input就是将get请求或post请求中所传的参数封装成一个Input对象，
$input = JFactory::getApplication()->input;
// 从input中获取task参数所对应的值并执行后续逻辑，这个下文紧接着会有介绍
$controller->execute($input->getCmd('task'));
// 下面这句，博主本来想直接略去，但是考虑到joomla官网教程有，就放这儿了，它的作用是：如果在这个过程中controller被要求要重定向到其他页面，则在前面逻辑执行完之后会进行跳转，这个留给大家自己去实验
$controller->redirect();
</code></pre>

除了上段代码中的注释部分外，博主还想说几句：

* 检查_JEXEC的存在实际上是为了防止直接执行这个文件，实际上这个变量只在joomla的总入口中（即：joomla根目录/index.php）有定义，其作用不言自明
* [Input API文档][jommla-input]

显然，下面这行代码执行了之后的主要逻辑：
<pre><code>$controller->execute($input->getCmd('task'));</code></pre>

现在有两个问题：

1. 这个controller是从哪里来的？
2. 这个task是个什么鬼，有什么作用？

我们逐个解决，首先来看问题1：

首先在入口处有这么一句：  
<pre><code>
$controller = JControllerLegacy::getInstance('Publish'); 
</code></pre>

这里Joomla实际上是后续通过字符串拼接找到了我们在controller.php定义的<code>PublishController</code>类

PublishController：
<pre><code>
&lt?php
/**
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author  diphuaji
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * Hello World Component Controller
 *
 * @since  0.0.1
 */
class PublishController extends JControllerLegacy
{
}
</code></pre>
是的，你没有看错，这里就直接继承了JControllerLegacy（Joomla自己的东西基本上都是J打头），现在就让我们挖深一些，看看这个JControllerLegacy到底都干了些啥：
<pre><code>

</code></pre>


现在很有必要看看controller的这个execute方法到底干了什么：

1. 地方
2. df
3. 



在整个joomla的component开发过程中，文件命名都非常重要，很多时候错一个字母，很可能就会导致该component的某些部分失效甚至报错
>在这里再次吐槽一下一个joomla对于开发者不太友好的地方，很多东西都在代码里面进行了定义，各种潜规则，坑点不少；
作为二次开发人员，当然应该去看joomla源码，从而了解一些深层次的运作机制，但是像文件名拼接查找逻辑这种只要稍微在文档中提示一下的东西，本就应该专门做一个专题来说明，写几条简单的规则，大家一看都能明白，皆大欢喜，不过博主貌似没有发现类似的东西。

###views目录
#参考文献
* [Developing a MVC Component/Introduction](https://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1)
[jommla-input]:https://api.joomla.org/cms-3/classes/JApplicationCms.html