<?php
namespace Lion\Logics\Producer;
use Lion\LionTraits\RedisConnectCommandTrait;

class RedisDataLogic extends DataTypeLogic  {

    use RedisConnectCommandTrait;
    private $data;

    public function __construct($data)
    {
        $this->data=$data;
    }

    /**
     * produce
     * @param $data
     */
    public function produce()
    {
        $topic=$this->data['topic'];
        unset($this->data['topic']);
        $job_data=$this->combine_data($this->data);
        $this->push($topic,$job_data);
    }

    /**
     * combine data
     * @param $push_data
     * @return string
     */
    public function combine_data($push_data){
        $queue_data=[
            'data'=>$push_data['data'],
            'create_at'=>$push_data['time'],
            'id'=>$push_data['id']
        ];
        return json_encode($queue_data);
    }

    /**
     * real execute remove command
     * @param $queue
     * @param $contents
     */
    private function push($queue,$contents){
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        $driver = lion_config('enable_driver');
        $redis_config = lion_config($driver);
        $host=$redis_config['host'];
        $port=$redis_config['port'];
        if (!$client->connect($host, $port, -1)) return ;

        //connect redis
        $connect_command=$this->get_connect_command();
        $client->send($connect_command);

        //set command
        $push_command=$this->get_push_command($queue,$contents);
        $client->send($push_command);
        $client->close();
    }

    /**
     * get push command
     * @param $queue
     * @param $contents
     * @return string
     */
    public function get_push_command($queue,$contents){
        $push_args = [$queue,$contents];
        $command_push = $this->get_command('LPUSH', $push_args);
        return $command_push;
    }
}