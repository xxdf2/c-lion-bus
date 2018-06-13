<?php
namespace Lion\Logics\Consumer;
use Lion\LionTraits\RedisConnectCommandTrait;
class AfterHandleRedisLogic implements AfterHandleLogic {
    use RedisConnectCommandTrait;

    /**
     * after handle
     * @param $result
     * @param $data
     */
    public function handle($result,$data){
        //if job was succeed, then remove data from fail queue
        if($result) $this->remove_fail($data);
    }

    /**
     * remove fail data
     * @param $content
     */
    private function remove_fail($content){
        $fail_queue=$content['topic_fail_name'];
        unset($content['topic_fail_name']);
        $content=json_encode($content);
        $this->_remove_fail($fail_queue,$content);
    }

    /**
     * real execute remove command
     * @param $queue
     * @param $contents
     */
    private function _remove_fail($queue,$contents){
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        $driver = lion_config('enable_driver');
        $redis_config = lion_config($driver);
        $host=$redis_config['host'];
        $port=$redis_config['port'];
        if (!$client->connect($host, $port, -1)) return ;

        //connect redis
        $connect_command=$this->get_connect_command();
        $client->send($connect_command);

        //get lrem command
        $lrem_command=$this->get_lrem_command($queue,$contents);
        $client->send($lrem_command);
        $client->close();
    }

    /**
     * Get LREM Command
     * @param $queue
     * @param $contents
     * @return string
     */
    private function get_lrem_command($queue,$contents){
        $lrem_args = [$queue,0,$contents];
        $command_LREM = $this->get_command('LREM', $lrem_args);
        return $command_LREM;
    }
}

