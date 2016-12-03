#介绍
在结合个人实际经验、其它大神以及JOOMLA官方文档的基础上，本文旨在为想要学习3.X component开发的朋友们做一下力所能及的知识分享，若有谬误，还请指出。

为了能正常看到成果，文目标读者必须具备以下能力：

* 了解HTTP传输协议
* 了解最基本的网络编程概念，比如端口 
* 会配置最基本的WEB服务，并能区别HTTP服务（如：nginx）和程序服务(如:php-fpm)的区别
* 如果以上内容有不明白的，能自行百度（我保证百度就够了，google、bing啥的还用不着）

>本人用的是JOOMLA 3.6大版本，但3.X系列相互之间的差距应该不会太大（JOOMLA官网也并没有将3.X各个版本区分开来提供不同的文档），若在使用本文作为指导的情况下在其它版本中遇到问题，欢迎留言 


#Joomla中的MVC简介
MVC的概念，本文就不赘述了。在JOOMLA的component中，Model、View、Controller分别以三种不同的类（及其子类）代表，这三个类分别是LegacyModel、LagecyView、LegacyController，

关于LagecyView、LegacyModel、LegacyController这三个类，博主不再这里分析，说一句不博主自己都不太喜欢的话：请下源码自己看。如果按照JOOMLA自身的框架逻辑来说，初学不太需要去仔细研究源码，只要明白并尊重关键的命名规范就可以做很多事了。当然，想要深入理解JOOMLA框架的同志就必须要去跟源码（debug）了，其中涉及到了太多的目录名、类名等等的字符串拼接；在debug所使用的工具方面，这里个人只用过Xdebug，PHP比较知名的debug工具貌似就是它和Zend Debugger了。

M、V、C三者在JOOMLA中的联系可用下图表示：

![joomla-mvc](https://docs.joomla.org/images/9/9e/MVC_joomla.png)
**图1**

接下来从比较宏观的角度来介绍下JOOMLA component中M、V、C的概念及其作用（在这里只是针对**用户自主开发**的component而言）：

* Model
    * Model层直接管理WEB后端的数据、逻辑以及一些应用规则
    * 在JOOMLA中，Model层一般是由View层直接调用获取数据
* View
    * View层主要用于处理从Controller层引入的请求以及这之后相关数据的展示
    * 在JOOMLA中，View层负责了相当一部分的计算工作
* Controller
    * Controller层主要作用是根据客户端的输入数据（如：http中get/post方法传入的数据）调用相应的View层逻辑来处理用户请求
    * 在JOOMLA中，Controller一般不直接与Model层发生联系（从图1中的虚线可以看出），且其中涉及的计算与View、Model层相比较少，如果需求不十分复杂，甚至可以不用写多少代码，直接继承LegacyController即可
>PS：关于图1中的箭头，博主认为是数据的流动，而不是调用关系的指向

#命名规则
ikoDotA的博文教程中对于M、V、C三部分所涉文件夹以及文件的命名规则，做了一个简单介绍，并附上了给了一个链接。
在看过那个链接的内容之后，博主认为很有必要对其进行展开叙述，但考虑到在没有实际范例的情况下很直观的说明，因而将在后续教程中逐渐引入。

记得之前学习MAVEN的时候，官网教程里面有一句话让我印象深刻：如果你真的想从一个体系中受到最大的益处，请尽量遵守该系统的规则，切勿试图打破已经建立好的规则。

任何成熟并且被广大用户所接受的体系都有其自身的稳固性，其中必然存在着已经建立好的“规则”，而作为普通用户，提高工作效率、节省时间必然是主要目的，因而按照规矩办事无疑是比较明智的做法。

当然大牛也许可以打破原有的某些规则，从而派生出让一些更先进的体系，这不在本文讨论范围内。

#component的目录结构
为了防止出现一脸懵逼的情况（博主刚开始接触的时候就是这种状态），这里简单介绍下component在Joomla根目录中的相对位置：
joomla根目录   
<pre>
/opt/joomla-tutorial
├── **administrator**  
│   ├── cache  
│   │   └── index.html  
│   ├── **components**  
│   │   ├── com_admin  
│   │   ├── com_ajax  
│   │   └── ...  
├── bin  
├── cache  
├── cli  
├── **components**  
│   ├── com_ajax  
│   ├── com_banners  
│   └── ...  
├── htaccess.txt  
├── images  
├── includes  
├── index.php  
├── installation  
├── **language**  
├── layouts  
├── libraries  
├── LICENSE.txt  
├── media  
├── modules  
├── plugins  
├── README.txt  
├── robots.txt.dist  
├── templates  
├── tmp  
└── web.config.txt  
</pre>
以上就是本人测试环境下的joomla目录结构，可以看到每个component都对应了两套代码（暂且用这个词吧，或者可以叫做应用场景？如图中的加粗部分），分别对应管理后台（网站域名/administrator，非管理员无法登录查看）与外部网站（所有人可见）。

其中管理后台部分的代码在administrator/components下，而外部网站部分代码则是直接位于components目录下；另外本教程涉及到的重要目录还有language目录，用于处理多语言需求。

# 本教程所涉的用户需求
本教程所涉及到的功能如下：

* 外部网站（site）
    * 普通访客可以注册
    * 普通访客可以参与投票、浏览文章
    * 已注册用户可发言、发表评论
* 管理后台（admin）
    * 网站内部人员专用，用于发表投票、文章
    * 注册用户、普通访客无法访问

>博主将尽可能用上joomla中component相关的所有技术  
>本教程的代码都放在 <code>/opt/joomla-tutorial </code>下，教程中可能会出现

功能需求明确之后，本系列教程实操部分的结构大致如下（在最终版本前随时可能修改或者增删）：
## 1. component外部网站(site)： 新建一个简单的新建一个简单的外部页面（site）及其目录结构概览
## 2. component外部网站(site)： 创建不同的layout页面
## 3. component外部网站(site)： controller 与 子controller
## 4. component外部网站(site)： 数据界限——创建多个model类
## 5. component管理后台(admin)： 与site结构类似的开发逻辑

#参考文献
* [ikoDotA の BLOG](http://www.cnblogs.com/ikodota)
* [Developing a MVC Component/Introduction](https://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1)
* [Model–view–controller](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
