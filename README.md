# microbase hyperf基类
# 目录结构
/Annotation 
- 自定义注解与asp配合使用

/Aspect 接口调用层
- 自定义asp
- RedisAspect.php 监听redis操作,视环境可适配
- ResponseResultAspect.php 公共返回体{"data":{},"code":0,"message":"success"}

/Rewrite 基于hyperf的自定义重写
- 现在主要基于server,client添加了执行过程的事件

/Listener  
- 现在主要基于server,client添加了执行过程的日志