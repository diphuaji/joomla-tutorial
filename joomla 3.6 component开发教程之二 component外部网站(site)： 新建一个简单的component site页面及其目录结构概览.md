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
对照controller中d对照controller中display方法涉及的5个关键步骤，isplay方法涉及的5个关键步骤，
如果一切顺利，安装完毕后直接输入<code>你的域名/index.php?option=com_publish</code>，应该就能看到Hello World了。
##component部分的目录结构概览（即zip包中的目录结构）
大家大概已经注意到，插件安装完成后，不同的目录和文件都安装到joomla项目下不同的地方去了，相对位置和在zip包内的相比发生了变化，现在我们一个个把他们找出来：
###publish.xml文件
此文件为component配置文件，每个component都有自己的配置文件，其中包含了该component的基本信息（如：作者联系方式、使用条款等）以及相关文件在zip包中的相对位置；

关于此文件的内容，请登录博主[github主页][github-home]获取

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
从上文中的目录概览中我们可以看出，除了配置文件之外，一个component包含两大部分（见教程一）。
###component入口：publish.php
每个component都有且只有一个入口，在本教程中，这个文件的名字就是<code>publish.php</code>，要注意的是：名字不能错，不然joomla程序就找不到该component的位置了，我们为它写的所有逻辑都无法得到体现。

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

这里Joomla实际上是后续通过字符串拼接找到了我们在<code>controller.php</code>中定义的<code>PublishController</code>类。
> 至于怎么拼接的，大家可以看看<code>JControllerLegacy</code>这个类的源码，后续争取提出来说一下，这里考虑篇幅就掠过了。

####controller的执行逻辑
**controller.php：**
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
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
</code></pre>

在获取了一个PublishController实例之后（由于我们并没由写新的内容，这里其实就是JControllerLegacy的马甲），接着又拿到了一个<code>$input</code>，这个实际上是joomla中封装好的<code>Joomla\Input</code>实例，用于获取http请求中的参数（考虑到安全因素，它额外加了一层filter，这个是后话）；  
从请求中获取了<code>task</code>参数后，紧接着执行了execute方法，现在有必要看看这个方法到底干了什么：

JControllerLegacy:
<pre><code>
...

public function execute($task)
{
    $this->task = $task;

    $task = strtolower($task);

    if (isset($this->taskMap[$task]))
    {
        $doTask = $this->taskMap[$task];
    }
    elseif (isset($this->taskMap['__default']))
    {
        $doTask = $this->taskMap['__default'];
    }
    else
    {
        throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_TASK_NOT_FOUND', $task), 404);
    }

    // Record the actual task being fired
    $this->doTask = $doTask;

    return $this->$doTask();
}

...
</code></pre>

1. 首先将<code>task</code>参数传递给<code>execute</code>方法
2. controller拿到参数中的task，在taskMap中进行查询
3. 若查到相应的task方法，则将方法名赋值给doTask属性，否则使用默认task，若无特别定义，则默认task就是display

####从controller到view
如教程一所述，**controller一般不直接调用model，间接通过view进行调用**，在controller的执行逻辑最后一步执行了task方法，本章中由于并没由写自定义的方法，因而默认执行display方法：

JControllerLegacy：
<pre><code>
...

    public function display($cachable = false, $urlparams = array())
    {
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');        
        ...
        
        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
        
        if ($cachable && $viewType != 'feed' && JFactory::getConfig()->get('caching') >= 1)
        {
           ...
        }
        else
        {
            $view->display();
        }
        return $this;
    }

...
</code></pre>

如上段代码所示，display方法将接力棒递给了view，其中有几个关键步骤：

1. <code>$document</code>是view用来解析页面的参数，知道即可
2. <code>$viewType</code>得到的是'html'
3. <code>$viewName</code>得到的是'publish'
4. <code>$viewLayout</code>得到的是'default'
5. <code>$this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout))</code>，这个方法返回的是一个PublishViewPublish实例

> 省略号涉及一些额外的判断逻辑，这里不展开说明，就本章而言，不难发现最终会走到<code>$view->display();</code>这一步


至此我们将目光转移到view上，那么问题来了——PublishViewPublish类就定定义在哪？

我们至此移步到views目录。

###views目录
views目录放的自然是MVC中的V相关文件，这个view目录实际上是一个view的集合，每个view都存放在一个目录中，由于本例中只有一个view，因此只有publish一个目录。

####publish目录

首先来回答上文中未解答的问题：PublishViewPublish定义在哪？ 答案是：PublishViewPublish定义在view.html.php中。

那么另一个问题来了——joomla是怎么找到这个类的？

答案：<code>getView</code>方法拼的！

OK，那我们就看看这个拼接查询逻辑：

>由于字符串拼接的那部分代码看着实在是头晕，故这里只列出了大体逻辑，具体实现方法请看源码

对照controller中display方法涉及的关键步骤，view的查询逻辑如下：

1. 根据<code>$viewName</code>的值('publish')定位到<code>views/publish</code>目录
2. 根据<code>$viewType</code>定位到views/publish/view.html.php
3. 将该controller自己的类名按照$prefix."Controller的方式分解"，本例中是"PublishController"，因此$prefix就是"Publish"
4. 拿着3中获取到的prefix在2中定位到的文件查询类<code>PublishViewPublish</code>，这个名字获取的公式是： $prefix."View".ucfirst($viewName)

**view.html.php：**
<pre><code>
&lt?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the publish Component
 *  @author diphuaji
 * @since  0.0.1
 */
class PublishViewPublish extends JViewLegacy
{
    private $msg;
    /**
     * Display the Hello World view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        // Assign data to the view
        $this->msg = 'Hello World';

        // Display the view
        parent::display($tpl);
    }
}
</code></pre>

从以上代码中可以看到，PublishViewPublish中的display方法实际上直接调用了父类的（注意这里**$tpl**是空，后面会提到），大家应该猜到了，真正进行页面解析的部分就是这里，就快接近真相了，我们再看父类中的定义：

JViewLegacy：

<pre><code>
...

    public function display($tpl = null)
    {
        $result = $this->loadTemplate($tpl);

        if ($result instanceof Exception)
        {
            return $result;
        }

        echo $result;
    }

...
</code></pre>

继续追踪<code>loadTemplate</code>方法：

JViewLegacy：

<pre><code>
...

    public function loadTemplate($tpl = null)
    {
        ...
        $layout = $this->getLayout();
        $file = isset($tpl) ? $layout . '_' . $tpl : $layout;
        ...

        // Load the template script
        jimport('joomla.filesystem.path');
        $filetofind = $this->_createFileName('template', array('name' => $file));
        $this->_template = JPath::find($this->_path['template'], $filetofind);

        // If alternate layout can't be found, fall back to default layout
        if ($this->_template == false)
        {
            $filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
            $this->_template = JPath::find($this->_path['template'], $filetofind);
        }

        if ($this->_template != false)
        {
            // Unset so as not to introduce into template scope
            unset($tpl);
            unset($file);

            // Never allow a 'this' property
            if (isset($this->this))
            {
                unset($this->this);
            }

            // Start capturing output into a buffer
            ob_start();

            // Include the requested template filename in the local scope
            // (this will execute the view logic).
            include $this->_template;

            // Done with the requested template; get the buffer and
            // clear it.
            $this->_output = ob_get_contents();
            ob_end_clean();

            return $this->_output;
        }
        else
        {
            throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
        }
    }

...
</code></pre>

上段代码中略去了寻找模板的逻辑，模板是Joomla CMS中一个非常核心的模块，模板的意义之一就是让同样的内容以不同的风格展现给用户，joomla中的模板相关内容完全值得另开一个教程，因此这里不深入讨论。现在只要记住我们只有一套模板就好，这套模板放在 **<code>views/pubish/tmpl</code>** 中。

让我们简单梳理下上段代码的逻辑：

1. 找到要解析的文件名$file，由于$tpl是空，$file就是$layout，而getLayout()默认返回"default"（JControllerLegacy 的display方法早已在获取view的时候将"default"作为参数传了进去，详见 **_从controller到view_**；另外，default经常被作为joomla中默认文件的名字，后面还会看到），所以<>code$file="default"</code>
2. <code>$_template</code>属性就是最终要include进来的模板文件名(php 文件本身就是一个网页模板，也是php语言的设计初衷之一)，值为"default.php"
3. 在template目录中查找"default.php"文件是否存在
4. 由于在tmpl目录中存在default.php，则通过include引用（否则返回500错误页面），因此可以直接调用在<code>PublishViewPublish</code>中的成员，如本例中的$msg

>这里注意：为了将解析结果返回，而不是直接打印，上面用到了php的<code>ob_start、ob_get_contents、ob_end_clean</code>方法将结果赋值给<code>$_output</code>属性传回


至此，component的整个流程就走完了，访问：http://172.0.0.1/index.php?option=com_publish 就可以看到 Hello World! 了。

##小结

本章以site部分为例，介绍了component的运作方式，并穿插介绍了相关的文件，另外还简单分析了相关joomla源码；除了MVC中M，C和V都已介绍，M将在下章讲解。

在整个joomla的component开发过程中，文件命名都非常重要，很多时候错一个字母，很可能就会导致该component的某些部分失效甚至报错。
>关于命名这块，各种潜规则，坑点不少；
作为二次开发人员，当然应该去看joomla源码，从而了解一些深层次的运作机制，但是像文件名拼接查找逻辑这种只要稍微在文档中提示一下的东西，本就应该专门做一个专题来说明，写几条简单的规则，大家一看都能明白，皆大欢喜，不过博主貌似没有发现类似的东西。

#参考文献
* [Developing a MVC Component/Introduction](https://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1)
[jommla-input]:https://api.joomla.org/cms-3/classes/JApplicationCms.html
[github-home]:https://github.com/diphuaji/joomla-tutorial/tree/master/lesson-1/components/com_publish