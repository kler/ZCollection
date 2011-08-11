<?php
require_once 'App/Test/Case.php';

class Parent_ {

    private $priv;

    private $hor;

    protected $prot;

    public $pub;

    public function __construct() {
        $this->priv = 1;
        $this->prot = 2;
        $this->pub = 3;
    }

    public function __wakeup() {
        $m = 1;
        if (isset($this->_helios))
        unset($this->_helios);
    }
}

class Child_ extends Parent_{

    protected $priv;

    public function __construct() {
        $this->priv = 7;
        $this->_helios = new Parent_();
    }

    protected function ger() {
        echo ('s');
    }
}


class Object_CastTest extends App_Test_Case {

    public function testSerializedCast() {


        $b = new Child_();
        //$b = serialize($b);

        $this->ClassTypeCast($b, 'Parent_');
         $d = new Child_();

        $c = unserialize('O:7:"Parent_":4:{s:7:"');


        $b = method_exists($b, 'ger');
        $m  = 1;
    }

    public function ClassTypeCast(&$obj,$class_type) {
        if (class_exists($class_type,true)) {
            $obj = unserialize(
                preg_replace(
                    "/^O:[0-9]+:\"[^\"]+\":/i",
                    "O:".strlen($class_type).":\"".$class_type."\":", serialize($obj)
                )
            );
        }
    }


}