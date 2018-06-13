<?php
namespace Lion;

use Lion\Logics\Producer\DataTypeFactoryLogic;

class LionProducer
{
    /**
     * produce method
     * @param $topic
     * @param $msg
     */
    public function produce($topic,$msg)
    {
        if (empty($driver = lion_config('enable_driver')) || empty($topic) || empty($msg)) exit("Hey Buddy,Param Missing\n");

        $data['time'] = date('Y-m-d H:i:s', time());
        $data['data'] = $msg;
        $data['id'] = lion_get_uniqid();
        $data['topic'] = $topic;
        $instance = DataTypeFactoryLogic::get_instance($driver, $data);
        $instance->produce();
    }
}