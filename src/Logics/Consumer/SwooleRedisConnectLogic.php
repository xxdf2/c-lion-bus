<?php
namespace Lion\Logics\Consumer;

use Lion\LionTraits;

class SwooleRedisConnectLogic extends SwooleConnectLogic
{
    use LionTraits\RedisConnectCommandTrait;
    private $redis_host, $redis_port, $timeout;
    private $swoole_client;
    private $topic_name,$topic_fail_name;

    /**
     * SwooleRedisConnectLogic constructor.
     * @param $topic_name
     */
    public function __construct($topic_name)
    {
        //pre check
        $driver = lion_config('enable_driver');
        $redis_config = lion_config($driver);
        $this->middleware($redis_config);

        //redis config
        $this->redis_host = $redis_config['host'];
        $this->redis_port = $redis_config['port'];
        $this->timeout = $redis_config['timeout'];

        //swoole client
        $this->swoole_client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        //max execute times
        $this->max_execute = empty($data['max_execute']) ? 5000 : $data['max_execute'];

        //listen topics
        $this->topic_name = $topic_name;
        $this->topic_fail_name = $this->topic_name.':fail';
    }

    /**
     * pre check
     * @param $data
     */
    private function middleware($data)
    {
        //check param
        if (empty($data['host']) || !isset($data['port']) || !isset($data['timeout'])) {
            exit("Hey Buddy,Redis Param Missing\n");
        }

        //check swoole
        if (!class_exists('\swoole_client')) {
            exit("Hey Buddy,Swoole Not Installed\n");
        }
    }

    /**
     * lion start function
     */
    public function run()
    {
        //connect to redis server
        $connect_command = $this->get_connect_command();
        $this->swoole_client->on("connect", function ($cli) use ($connect_command) {
            $cli->send($connect_command);
        });

        //receive message from redis server
        $pop_command = $this->get_pop_command();
        $this->swoole_client->on("receive", function ($cli, $data) use ($pop_command){
            //handle data
            $parsed_data = $this->parse_reply($data);
            if ($parsed_data['pre'] == 'queue') {
                $this->handle_data($parsed_data);
            }
            $cli->send($pop_command);
        });

        //if error
        $this->swoole_client->on("error", function ($cli) {
            echo "error\n";
        });
        //if close
        $this->swoole_client->on("close", function ($cli) {
            echo "Connection close\n";
        });

        $this->swoole_client->connect($this->redis_host, $this->redis_port);
    }

    /**
     * get pop command
     * @return string
     * @throws \Exception
     */
    private function get_pop_command()
    {

        if(empty($this->topic_name) || empty($this->topic_fail_name)){
            exit("Hey Buddy,Redis Param Missing\n");
        }

        //list pop
        $queue_args = [$this->topic_name, $this->topic_fail_name, $this->timeout];
        $command_queue = $this->get_command('BRPOPLPUSH', $queue_args);

        return $command_queue;
    }

    /**
     * handle the data
     * @param $data
     */
    private function handle_data($data)
    {
        $queue_args = json_decode($data['reply']['queue_args'], true);
        if (!empty($queue_args['data'])) {
            $handler = new HandleDataLogic();
            $handler->handle($queue_args, $this->topic_name, $this->topic_fail_name);
            unset($handler);
            $this->check_exit();
        }
    }
}