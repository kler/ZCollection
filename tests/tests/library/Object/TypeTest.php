<?php
require_once 'App/Test/Case.php';

class Object_TypeTest extends App_Test_Case {

    public function testScalars() {

        $int = new Object_Type(5);

        $this->assertTrue($int->equals(5));
    }
}