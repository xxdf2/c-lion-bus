<?php

namespace Lion\LionTraits;

Trait RedisTcpProtocolTrait
{
    public $CRLF="\r\n";

    /**
     * get command
     * @param string $keyword
     * @param array $args
     * @param int $force_return
     * @return string
     */
    public  function get_command($keyword='',Array $args,$force_return=0){


        if(empty($keyword) && (empty($args) && $force_return==0)){
            return '';
        }

        //prefix command
        $head_command=$this->get_arr_command($args,1);#1 for keyword

        //keyword command
        $keyword=strtoupper(trim($keyword));
        $keyword_command=$this->get_string_command($keyword);

        //arg command
        $arg_command='';
        foreach ($args as $arg){
            $arg_command.=$this->get_string_command($arg);
        }

        //last command
        $last_command=$head_command.$keyword_command.$arg_command;
        return $last_command;
    }

    /**
     * string command
     * @param $string
     * @return string
     */
    private function get_string_command($string){
        $str_len=strlen($string);
        $command="$".$str_len.$this->CRLF.$string.$this->CRLF;
        return $command;
    }

    /**
     * array command
     * @param array $args
     * @param int $plus
     * @return string
     */
    private function get_arr_command(Array $args,$plus=0){
        $count_args=count($args);
        $count_all=$count_args+$plus; #plus extra command count
        $command="*".$count_all.$this->CRLF;
        return $command;
    }

    /**
     * parse redis server reply
     * @param $reply
     * @return array
     */
    public function parse_reply($reply){
        $data_pre=substr($reply, 0, 1);
        $data_arr=array_filter(explode($this->CRLF,$reply));
        //if queue
        if(($data_pre=="$")){
            $queue_args=$data_arr[1];
            return ['pre'=>'queue','reply'=>['queue_args'=>$queue_args]];
        }
        return ['pre'=>'other','reply'=>trim($reply)];
    }
}
