<?php
class Object_Bool implements Object_Boxed {

    protected final $_boxed;

    public function __construct($var) {

        if (!is_bool($var)) {
            throw new Object_Exception('Not an boolean');
        }

        $this->_boxed = $var;
    }

    public function getHash() {

        return Object_Type::hash($this->_boxed);

    }

    public function compare($a) {

        $a = (int) a;
        $b = (int) $this->_boxed;

        if ($b > $a) {
            return 1;
        } else if ($b < $a) {
            return -1;
        }

        return 0;

    }

    final public function type() {
        return Object_Boxed::_BOOL;
    }

    final public function __toString() {

        return (string) $this->_boxed;

    }

}