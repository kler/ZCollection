<?php
class Collection_LinkedList_Node_Attach extends Collection_LinkedList_Node {

    /**
     *
     * @var Collection_LinkedList_Node
     */
    protected $_wrap;

    public function __gc() {
        $this->_wrap = null;
        $this->_collection = null;
        $this->_next = null;
        $this->_previous = null;
        $this->state = null;
    }

    public function wrap(Collection_LinkedList_Node_Attach $node) {
        $this->_wrap = $node;
        return $this;
    }

    public function unwrap() {
        $wrap = $this->_wrap;
        $this->_wrap = null;
        $this->_collection = null;
        return $wrap;
    }

    public function attach(Collection_Node_Collection $collection) {
        $this->_collection = $collection;
        $this->_wrap->remove();
        $this->_wrap->_collection = $collection;
        return $this;
    }
}