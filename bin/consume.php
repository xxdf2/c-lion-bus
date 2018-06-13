<?php
$vendor_path=dirname(dirname(dirname(__DIR__)));
require_once $vendor_path.'/autoload.php';

use Lion\LionConsumer;

$topic=getenv('TOPIC');
if(empty($topic)) exit("Please Configure Your Topic\n");

$consumer=new LionConsumer($topic);
$consumer->consume();