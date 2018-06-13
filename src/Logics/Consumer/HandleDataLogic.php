<?php
namespace Lion\Logics\Consumer;

use Lion\LionTraits\RedisConnectCommandTrait;

class HandleDataLogic
{
    use RedisConnectCommandTrait;

    /**
     * handle job
     * @param $queue_args
     * @param $topic_name
     * @param $topic_fail_name
     * @return bool
     */
    public function handle($queue_args, $topic_name, $topic_fail_name)
    {
        //execute command
        $data = json_encode($queue_args['data']);
        $command = lion_config('command')[$topic_name];
        $return = exec($command . ' ' . "'$data'");
        //rewrite response
        $result = false;
        if (empty($return)) $result = true;

        //statistic and after handle job logic
        $this->statistic($result, $topic_name);
        $this->after_handle($result, $queue_args, $topic_fail_name);

        return $result;
    }

    /**
     * statistic
     * @param $result
     * @param $topic_name
     */
    private function statistic($result, $topic_name)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        $driver = lion_config('enable_driver');
        $redis_config = lion_config($driver);
        $host = $redis_config['host'];
        $port = $redis_config['port'];
        if (!$client->connect($host, $port, -1)) return;

        //connect redis
        $connect_command = $this->get_connect_command();
        $client->send($connect_command);

        //incr statistic key
        $key = $topic_name . ":count:fail";
        if ($result) $key = $topic_name . ":count";

        $incr_command = $this->get_incr_command($key);
        $client->send($incr_command);
        $client->close();
    }

    /**
     * get incr command
     * @param $key
     * @return string
     */
    private function get_incr_command($key)
    {
        $incr_args = [$key];
        $command_auth = $this->get_command('incr', $incr_args);
        return $command_auth;
    }

    /**
     * after handle job
     * @param $result
     * @param $data
     * @param $topic_fail_name
     */
    private function after_handle($result, $data, $topic_fail_name)
    {
        $driver = ucfirst(lion_config('enable_driver'));
        $instance = AfterHandleFactoryLogic::get_instance($driver);
        $data['topic_fail_name'] = $topic_fail_name;
        $instance->handle($result, $data);
    }
}