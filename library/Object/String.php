<?php
class Object_String implements Object_Boxed {

    protected final $_boxed;

    public function __construct($var) {

        if (!is_string($var)) {
            throw new Object_Exception('Not a string');
        }

        $this->_boxed = $var;
    }

    public function getHash() {

        return Object_Type::hash($this->_boxed);

    }

    public function compare($a) {

        $a = (string) a;

        return strcmp($this->_boxed, $a);

    }

    final public function type() {
        return Object_Boxed::_STRING;
    }

    final public function __toString() {

        return (string) $this->_boxed;

    }
}