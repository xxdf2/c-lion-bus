<?php
namespace Lion\Logics\Consumer;

class AfterHandleFactoryLogic{
    /**
     * Factory Pattern,Get After Handle Instance
     * @param $driver
     * @return object
     * @throws \Exception
     */
    public static function get_instance($driver){
        $driver=ucfirst($driver);
        $file_namespace='\\Lion\\Logics\\Consumer\\';
        $reflection=new \ReflectionClass($file_namespace.'AfterHandle'.$driver.'Logic');
        $object=$reflection->newInstance();
        if(!($object instanceof AfterHandleLogic)){
            throw new \Exception("Not AfterHandle Object!");
        }
        return $object;
    }
}