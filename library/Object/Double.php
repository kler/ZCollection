<?php
class Object_Double implements Object_Boxed {

    protected final $_boxed;

    public function __construct($var) {

        if (!is_double($var)) {
            throw new Object_Exception('Not a double');
        }

        $this->_boxed = $var;
    }

    public function getHash() {

        return Object_Type::hash($this->_boxed);

    }

    public function compare($a) {

        $a = (float) $a;

        if ($this->_boxed > $a) {
            return 1;
        } else if ($this->_boxed < $a) {
            return -1;
        }

        return 0;

    }

    final public function type() {
        return Object_Boxed::_DOUBLE;
    }

    final public function __toString() {

        return (string) $this->_boxed;

    }
}