<?php
require_once 'App/Test/Case.php';

class Object_TypeTest extends App_Test_Case {

    public function testScalars() {

        $int = new Object_Int(5);

        $this->assertTrue($int->compare(5) == 0);
    }
}