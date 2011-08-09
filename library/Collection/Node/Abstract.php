<?php
abstract class Collection_Node_Abstract {
    /**
     *
     * @var Collection_Node_Collection
     */
    protected $collection;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @var array
     */
    protected $types;

    protected $state;

    public function getState() {

        return (int) $this->state;

    }

    public function getHash()  {

        return Object_Type::hash($this);

    }

    /**
     *
     * @return Collection_Node_Collection
     */
    public function getCollection() {

        return $this->collection;

    }

    /**
     *
     * @return boolean
     */
    public function isOrphan() {

        return empty($this->collection);

    }

    /**
     * @param Collection_Node_Collection $collection
     * @return boolean
     */
    public function belongsTo(Collection_Node_Collection $collection) {

        if ($this->isOrphan() || $this->getCollection() !== $collection) {
            return false;
        }

        return true;

    }

    /**
     *
     * @param Collection_Node_Collection $collection
     * @return Collection_Node_Interface
     */
    public function attach(Collection_Node_Collection $collection) {

        if ($this->state == Collection_Node_Interface::STATE_LOCKED) {
            return $this;
        }

        if ($this->belongsTo($collection)) {
            return $this;
        }

        //prevent detaching is the node is empty
        if ($this->isEmpty()) {
            throw new Collection_Exception('Can not attach an empty node');
        }

        //prevent infinite looping
        $this->state = Collection_Node_Interface::STATE_LOCKED;

        if (!$this->isOrphan()) {
            $this->_remove();
        }

        //ask collection to attach, collection will validate if this is a legal move
        $collection->attachNode($this);
        $this->collection = $collection;

        //unlock
        $this->state = null;
        return $this;

    }

    /**
     * @return Collection_Node_Collection
     */
    public function remove() {

        if ($this->state == Collection_Node_Interface::STATE_LOCKED) {
            return $this;
        }

        if ($this->isOrphan()) {
            return $this;
        }

        //prevent infinite looping
        $this->state = Collection_Node_Interface::STATE_LOCKED;
        //ask collection to de-reference me
        $this->collection->removeNode($this);

         $this->_remove();

        //unlock
        $this->state = null;
        return $this;

        return $this;
    }

    /**
     * Performs a detach without any safe checks. To be called only internally
     *
     * @return Collection_Node_Collection
     */
    protected function _remove() {

        //de-reference the collection
        $this->collection = null;
        return $this;
    }

    /**
     * @param string|array $type
     */
    public function accepts($var) {

        if (!is_string($var) && !is_array($var)) {
            throw new Collection_Exception('invalid parameter type');
        }

        $var = (array) $var;
        $this->types = array_map(
            function($var){
                return (string) $var;
            },
            $var
        );

        return $this;

    }

    /**
     * @param mixed $var
     * @return boolean
     */
    public function canAccept($var) {

        if (!is_object($var) || empty($var)) {
            throw new Collection_Exception('invalid parameter type');
        }

        if (empty($this->types)) {
            return false;
        }

        $className = get_class($var);
        foreach ($this->types as $type) {
            if ($className == $type) {
                return true;
            }

            if (is_subclass_of($var, $className)) {
                return true;
            }
        }

        return false;

    }

    /**
     * @return boolean
     */
    public function isEmpty() {

        return empty($this->context);

    }

    /**
     * @param mixed $var
     * @return Collection_Node_Interface
     */
    public function setContext($var) {

        if (!$this->canAccept($var)) {
            throw new Collection_Exception('Invalid object type');
        }
        $this->context = $var;

        if (!$this->isOrphan()) {
            $this->getCollection()->onNodeChange($this, 'context');
        }

        return $this;

    }

    /**
     * @return mixed
     */
    public function getContext() {

        return $this->context;

    }

}