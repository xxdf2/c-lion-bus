<?php

namespace Lion\LionTraits;
use Lion\LionTraits\RedisTcpProtocolTrait;

Trait RedisConnectCommandTrait
{
    use RedisTcpProtocolTrait;
    /**
     * get connect command
     * @return string
     */
    public function get_connect_command()
    {
        $driver = lion_config('enable_driver');
        $redis_config = lion_config($driver);
        $redis_auth = $redis_config['auth'];
        $redis_db = $redis_config['db'];

        //auth
        $auth_args = [$redis_auth];
        $command_auth = $this->get_command('auth', $auth_args);

        //db
        $db_args = [$redis_db];
        $command_db = $this->get_command('select', $db_args);

        return $command_auth . $command_db;
    }
}