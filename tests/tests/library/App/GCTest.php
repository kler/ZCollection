<?php
require_once 'App/Test/Case.php';

class App_GCTest_Singleton {
    static protected $_instance;
    static public $instanceCount = 0;
    protected function __construct() {
        self::$instanceCount++;
    }

    static public function getInstance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
class App_GCTest extends App_Test_Case {

    public function testSingleton() {
        $instance = App_GCTest_Singleton::getInstance();
        App_GC::getInstance()->collect($instance);
        $instance = App_GCTest_Singleton::getInstance();

        $this->assertTrue(App_GCTest_Singleton::$instanceCount == 1);
    }
}