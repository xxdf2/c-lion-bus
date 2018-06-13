<?php
namespace Lion\Logics\Consumer;

class SwooleConnectFactoryLogic
{
    /**
     * Get SwooleConnect Instance(Factory Pattern)
     * @param $driver
     * @param $topic
     * @return object
     * @throws \Exception
     */
    public static function get_instance($driver,$topic)
    {
        $driver = ucfirst($driver);
        $file_namespace = '\\Lion\\Logics\\Consumer\\';
        $reflection = new \ReflectionClass($file_namespace . 'Swoole' . $driver . 'ConnectLogic');
        $object = $reflection->newInstance($topic);
        if (!($object instanceof SwooleConnectLogic)) {
            throw new \Exception("Not SwooleConnect Object!");
        }
        return $object;
    }
}