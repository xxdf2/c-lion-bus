<?php
namespace Lion\Logics\Producer;

class DataTypeFactoryLogic{
    /**
     * Get Data Type Instance
     * @param $driver
     * @param $data
     * @return object
     * @throws \Exception
     */
    public static function get_instance($driver,$data){
        $driver=ucfirst($driver);
        $file_namespace='\\Lion\\Logics\\Producer\\';
        $reflection=new \ReflectionClass($file_namespace.$driver.'DataLogic');
        $object=$reflection->newInstance($data);
        if(!($object instanceof DataTypeLogic)){
            throw new \Exception('PRODUCER NOT CORRECT DATA TYPE');
        }
        return $object;
    }
}