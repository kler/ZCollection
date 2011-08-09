<?php
class Collection_LinkedList_Node
    extends Collection_Node_Abstract
    implements Collection_Node_Interface
{

    /**
     * @var Collection_LinkedList_Node
     */
    protected $_next;

    /**
     * @var Collection_LinkedList_Node
     */
    protected $_previous;

    /**
     * @return Collection_LinkedList
     */
    public function getCollection() {

        return parent::getCollection();

    }

    public function attach(Collection_Node_Collection $collection) {

        if (!($collection instanceof Collection_LinkedList)) {
            throw new Collection_Exception('Invalid parameter type');
        }

        parent::attach($collection);
        return $this;

    }

    /**
     * @return Collection_LinkedList_Node
     */
    public function getNext() {

        return $this->_next;

    }

    /**
     * @return Collection_LinkedList_Node
     */
    public function getPrevious() {

        return $this->_previous;

    }

    public function setNext(Collection_LinkedList_Node $next) {

        //prevent infinite looping
        if ($this->state == self::STATE_LOCKED) {
            return $this;
        }

        if ($this->isOrphan()) {
            throw new Collection_Exception('Orphan nodes can not have previous nodes');
        }

        //if this is not an orphan it is also not empty
        if ($next->isEmpty()) {
            throw new Collection_Exception('Empty nodes can not linked together');
        }

        //locking
        $this->state = self::STATE_LOCKED;

        //here we need to detach from the previous collection to update the index
        if (!$next->isOrphan() && !$next->belongsTo($this->collection)) {
            $next->remove();
        }

        //check if this is tail, if it is we need to make the collection update its reference
        $isTail = $this->isTail();

        if (!$isTail) {
            $this->_next->_previous = $next;
            $next->_next = $this->_next;
        }
        $next->_previous = $this;
        $this->_next = $next;

        //insert in collection
        //$this->getCollection()->insertAfterNode($this, $next);

        //insert in collection, so that it gets indexed
        //lock it first
        //$next->state = Collection_Node_Interface::STATE_LOCKED;
        $next->attach($this->collection);
        //$this->collection->attachNode($next);
        //$next->state = null;

        //make the collection update it's reference to tail, should an event be fired?
        if ($isTail) {
            $this->collection->setTailNode($next);
        }

        //unlocking
        $this->state = null;
        return $this;

    }

    public function setPrevious(Collection_LinkedList_Node $previous) {

        //prevent infinite looping
        if ($this->state == self::STATE_LOCKED) {
            return $this;
        }

        if ($this->isOrphan()) {
            throw new Collection_Exception('Orphan nodes can not have previous nodes');
        }

        //if this is not an orphan it is also not empty
        if ($previous->isEmpty()) {
            throw new Collection_Exception('Can not link with empty node');
        }

        //locking
        $this->state = self::STATE_LOCKED;

        //here we need to detach from the previous collection to update the index
        if (!$previous->belongsTo($this->collection)) {
            $previous->remove();
        }

        $isHead = $this->isHead();

        if (!$isHead) {
            $this->_previous->_next = $previous;
            $previous->_previous = $this->_previous;
        }
        $previous->_next = $this;
        $this->_previous = $previous;

        //insert in collection, so that it gets indexed
        $previous->attach($this->collection);

        //make the collection update it's reference to head, should an event be fired?
        if ($isHead) {
            $this->collection->setHeadNode($previous);
        }

        //unlocking
        $this->state = null;
        return $this;

    }

    public function insertBefore(Collection_LinkedList_Node $node) {

        if ($node->isEmpty()) {
            throw new Collection_Exception('Can not insert before empty node');
        }

        $node->setPrevious($this);
        return $this;

    }

    public function insertAfter(Collection_LinkedList_Node $node) {

        if ($node->isEmpty()) {
            throw new Collection_Exception('Can not insert after empty node');
        }

        $node->setNext($this);
        return $this;

    }

    public function isHead() {

        return !$this->isOrphan()
            && !$this->isEmpty()
            && empty($this->_previous);

    }

    public function isTail() {

        return !$this->isOrphan()
            && !$this->isEmpty()
            && empty($this->_next);

    }

    protected function _remove() {

        parent::_remove();

        $next = $this->_next;
        $this->_next = null;

        $previous = $this->_previous;
        $this->_previous = null;

        if ($next && $previous) {
            $next->_previous = $previous;
            $previous->_next = $next;
            $next = null;
            $previous = null;
        } else if ($next) {
            $next->_previous = null;
            $next = null;
        }else if ($previous) {
            $previous->_next = null;
            $previous = null;
        }

        return $this;

    }

    protected function _evict() {

        parent::_evict();
        $next = $this->_next;
        $this->_next = null;

        $previous = $this->_previous;
        $this->_previous = null;

        if ($next) {
            $next->_previous = null;
            $next->_evict();
            $next = null;
        }

        if ($previous) {
            $previous->_next = null;
            $previous->_evict();
            $previous = null;
        }

        return $this;
    }
}