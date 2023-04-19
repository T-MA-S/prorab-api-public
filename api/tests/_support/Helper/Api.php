<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function clearTable($table){
        $db = $this->getModule('Db')->driver;
        $db->load(["TRUNCATE TABLE `$table`"]);
    }
}
