<?php
namespace Lion\Logics\Consumer;
interface AfterHandleLogic{
    /**
     * handle the job(interface)
     * @param $result
     * @param $data
     * @return mixed
     */
    public function handle($result,$data);
}