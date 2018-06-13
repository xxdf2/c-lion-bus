<?php
namespace Lion\Logics\Consumer;

abstract class SwooleConnectLogic
{
    abstract public function run();

    public $counter = 0;
    public $max_execute = 5000; # default execute max time

    /**
     * check if exit
     */
    protected function check_exit()
    {
        $this->counter++;
        if ($this->counter >= $this->max_execute) exit(0);
    }
}