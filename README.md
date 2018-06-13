## Lion - Speed & Strength

Lion belongs to Message Buses(Based On Swoole).

In lion,You don't need to care about the message media(redis,kafka,etc...)

The only thing you need to do is to send message and handle the message!

So simple and useful,just for you!

-----

Lion是消息总线的一种实现(基于Swoole实现)。

使用Lion，你不必关心消息的具体存储介质（redis，kafka等等）

你唯一需要做的事情就是发消息和处理消息。

就是这么简单、实用，为你而来！

![Lion][1]

---

# C-Lion-Bus使用教程


---
## 0、c-lion-bus使用说明
c-lion-bus目前版本为v0.1，以composer包的方式提供服务。

c-lion-bus提供了消息总线的功能-即是一个抽象的消息队列。

基于swoole的tcp长连接功能，从此告别while(true)的写法。

c-lion-bus基于可靠消息队列，提供了生产者、消费者的使用，以及简单的消息统计功能。

## 1、引入c-lion-bus

 - 执行如下命令，引入c-lion-bus 

```
composer require ericliu000/c-lion-bus dev-master --prefer-dist
```

## 2、建立c-lion配置文件
建立文件
```
/vendor/config/LionConfig.php
```

示例配置文件，参考

```
/vendor/ericliu000/c-lion-bus/src/Common/LionConfig.php.example
```

## 3、producer的使用方法

```
//$topic为该消息的topic。
//$data为向该topic推送的数据。

use Lion\LionProducer;
$lion = new LionProducer();
$lion->produce($topic,$data);
```

示例

```
use Lion\LionProducer;
$lion = new LionProducer();
$lion->produce('SignTopic',['sign'=>1,'check'=>true]);
```

## 4、comsumer的使用方法

```
1、在LionConfig.php中，配置consumer获取到该topic数据后要执行的命令
2、启动consumer
```

示例

```
1、示例为：在LionConfig.php中，配置topic=Sign 与 topic=Sms的command
'command'=>[
        'Sign'=>'cd /Users/ericliu000/Project/maibei-backend && php -f /Users/ericliu000/Project/maibei-backend/console.php /Index/Sign',
        'Sms'=>'cd /Users/ericliu000/Project/maibei-backend && php -f /Users/ericliu000/Project/maibei-backend/console.php /Index/Sms',
    ]
    
2、启动Topic=Sms的消费进程
TOPIC=Sms php vendor/ericliu000/c-lion-bus/bin/consume.php &

3、启动Topic=Sign的消费进程
TOPIC=Sign php vendor/ericliu000/c-lion-bus/bin/consume.php &
```


实际消费方法如下
```
//1、IndexController.class.php定义方法如下

public function Sign(){
        //$argv是一个数组，包含上面的数据即['sign'=>1,'check'=>true]
        global $argv;
        
        //Do Your Logic...
}

public function Sms(){
        //$argv是一个数组，包含上面的生产者生产的数据
        global $argv;
        
        //Do Your Logic...
}
```

有使用疑问请联系QQ:919696910




[1]: http://ww1.sinaimg.cn/large/0060lm7Tly1fmwgdodjwvj30hs0bf75i.jpg