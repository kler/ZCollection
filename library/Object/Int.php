<?php
class Object_Int implements Object_Boxed {

    protected final $_boxed;

    public function __construct($var) {

        if (!is_int($var)) {
            throw new Object_Exception('Not an integer');
        }

        $this->_boxed = $var;
    }

    public function getHash() {

        return Object_Type::hash($this->_boxed);

    }

    public function compare($a) {

        $a = (int) a;

        if ($this->_boxed > $a) {
            return 1;
        } else if ($this->_boxed < $a) {
            return -1;
        }

        return 0;

    }

    final public function type() {
        return Object_Boxed::_INT;
    }

    final public function __toString() {

        return (string) $this->_boxed;

    }

}