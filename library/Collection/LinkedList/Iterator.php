<?php
class Collection_LinkedList_Iterator implements Iterator{

    const ORDER_NORMAL = 2;
    const ORDER_REVERSE = 4;

    const VALID = 2;
    const REWINDABLE = 4;

    protected $_next;

    /**
     * @var Collection_LinkedList_Node
     */
    protected $_current;

    /**
     *
     * @var Collection_LinkedList
     */
    protected $_collection;

    protected $_state = 0;

    protected $_order;

    public function __construct(Collection_LinkedList $collection, $order=null) {

        if (empty($order)) {
            $order = self::ORDER_NORMAL;
        }

        if ($order != self::ORDER_NORMAL && $order != self::ORDER_REVERSE) {
            throw new Collection_Exception('Invalid iterator order');
        }

        $this->_order = $order;

        if (!$collection->isEmpty()) {
            $this->_state = self::VALID | self::REWINDABLE;
            $this->_init($collection);
        }

    }

    protected function _init(Collection_LinkedList $collection) {

        if ( $this->_order == self::ORDER_NORMAL) {
            $this->_current = $collection->getHeadNode();
            $this->_next = function (Collection_LinkedList_Node $node) {
                return $node->getNext();
            };
        } else if ($this->_order == self::ORDER_NORMAL) {
            $this->_current = $collection->getTailNode();
            $this->_next = function (Collection_LinkedList_Node $node) {
                return $node->getPrevious();
            };
        }

        $this->_collection = $collection;

    }

    public function current () {

        if (!$this->valid()) {
            return null;
        }

        return $this->_current->getContext();

    }

    public function next () {

        if (!$this->valid()) {
            return null;
        }

        if (!$this->_current->belongsTo($this->_collection)) {
            $this->_state = $this->_state ^ self::REWINDABLE;
            return;
        }

        $this->_current = $this->_next($this->_current);
        if (empty($this->_current)) {
            $this->_state  = $this->_state ^ self::REWINDABLE;
        }

    }

    public function key () {

        if (!$this->valid()) {
            return null;
        }

        return $this->_current->getHash();

    }

    public function valid () {

        if ($this->_state & self::VALID === 0) {
            return false;
        }

        return true;
    }

    public function rewind () {

        if ($this->_state & self::REWINDABLE === 0) {
            return;
        }

        if ($this->_order == self::ORDER_NORMAL) {
            $this->_current = $this->_collection->getHeadNode();
            return;
        }

        $this->_current = $this->_collection->getTailNode();
    }
}