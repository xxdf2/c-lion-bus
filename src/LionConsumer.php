<?php
namespace Lion;

use Lion\Logics\Consumer\SwooleConnectFactoryLogic;

class LionConsumer
{
    private $topic;

    public function __construct($topic)
    {
        $this->topic = $topic;
    }

    /**
     * consume method
     */
    public function consume()
    {
        if (empty($driver = lion_config('enable_driver'))) exit("Hey Buddy,Param Missing\n");
        $instance = SwooleConnectFactoryLogic::get_instance($driver,$this->topic);
        $instance->run();
    }
}