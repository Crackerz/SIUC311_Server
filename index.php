<?php 
class obj {
	public $name = "name";
	public $test = "example";
}

$test = new obj();
$str = json_encode($test);
$test = json_decode($str);
var_dump($test);
