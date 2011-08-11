<?php
class Collection_LinkedList
    implements Collection_Node_Collection, Collection_LinkedList_Interface {

    /**
     *
     * @var Collection_LinkedList_Node
     */
    protected $prototype;

    /**
     * @var array
     */
    protected $objects;

    /**
     * @var Collection_LinkedList_Node
     */
    protected $_head;

    /**
     * @var Collection_LinkedList_Node
     */
    protected $_tail;

    public function __construct(Collection_LinkedList_Node $prototype=null) {

        if (empty($prototype)) {
            $this->prototype = new Collection_LinkedList_Node();
        } else {
            $this->prototype = $prototype;
        }
    }

    public function isEmpty() {
        return empty($this->objects);
    }

    public function count() {

        if (empty($this->objects)) {
            return 0;
        }

        return count($this->objects);

    }

    public function canAccept($var) {

        return $this->prototype->canAccept($var);

    }

    public function contains($var) {

        $hash = $this->_getObjectHash($var);
        return isset($this->objects[$hash]);

    }

    public function append($var) {

        if (!is_object($var)) {
            throw new Collection_Exception('Invalid parameter type');
        }

        //create node
        $node = $this->_toNode($var);

        //orphan the node
        if (!$node->isOrphan() && !$node->belongsTo($this)) {
            $node->remove();
        }

        //no node yet in the list
        if(empty($this->_tail) && empty($this->_head)) {
            $this->_head = $node;
            $this->_head->attach($this);
            return $this;
        }

        //inser after tail
        $tail = $this->getTailNode();
        $tail->setNext($node);
        return $this;

    }

    public function prepend($var) {

        if (!is_object($var)) {
            throw new Collection_Exception('Invalid parameter type');
        }

        //create the node
        $node = $this->_toNode($var);

        //orphan the node
        if (!$node->isOrphan() && !$node->belongsTo($this)) {
            $node->remove();
        }

        if(empty($this->_head)) {
            $this->_head = $node;
            $this->_head->attach($this);
            return $this;
        }

        //insert before head
        $this->_head->setPrevious($node);
        return $this;

    }

    public function insertBefore($existing, $candidate) {

        if ($existing === $candidate) {
            throw new Collection_Exception('Can not insert.Objects are identical ');
        }

        $existingHash = $this->_getObjectHash($existing);

        if (!isset($this->objects[$existingHash])) {
            throw new Collection_Exception('Can not insert before a node that is not part of me');
        }

        $candidateNode = $this->_toNode($candidate);
        $existingNode = $this->objects[$existingHash];

        $existingNode->setPrevious($candidate);
        return $this;

    }

    public function insertAfter($existing, $candidate) {

        if ($existing === $candidate) {
            throw new Collection_Exception('Can not insert.Objects are identical ');
        }

        $existingHash = $this->_getObjectHash($existing);

        if (!isset($this->objects[$existingHash])) {
            throw new Collection_Exception('Can not insert before a node that is not part of me');
        }

        $candidate = $this->_toNode($candidate);
        $existingNode = $this->objects[$existingHash];

        $existingNode->setNext($candidate);
        return $this;

    }

    public function add($var) {
        return $this->append($var);
    }

    public function remove($var) {

        if (empty($this->objects)) {
            return $this;
        }

        if (!$this->contains($var)) {
            return $this;
        }

        $node = $this->_toNode($var);
        return $this->_remove($node);

    }

    public function removeAll() {

        if ($this->isEmpty()) {
            return $this;
        }

        $this->tail = null;
        while($this->_head) {
            $this->_remove($this->_head);
        }

        $this->objects = null;
        $this->nodes = null;

        return $this;

    }

    /**
     *
     * @return Iterator
     */
    public function getIterator() {
        return new Collection_LinkedList_Iterator($this);
    }

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_Node_Collection
     */
    public function appendNode(Collection_LinkedList_Node $node) {

        $tail = $this->getTailNode();

        if (empty($tail)) {
            $this->_head = $node;
            $this->onNodeChange($node, 'context');
            return $this;
        }

        $tail->setNext($node);
        $this->_tail = $node;

        return $this;
    }

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_Node_Collection
     */
    public function prependNode(Collection_LinkedList_Node $node) {

        $head = $this->getHeadNode();

        if (empty($head)) {
            $this->_head = $node;
            $this->onNodeChange($node, 'context');
            return $this;
        }

        $head->setPrevious($node);
        $this->_head = $node;

        return $this;
    }

    public function insertBeforeNode(
        Collection_LinkedList_Node $existingNode,
        Collection_LinkedList_Node $candidateNode
    ) {

        //TODO checking this first has some penalty issue when inserting
        if (!$existingNode->belongsTo($this)) {
            throw new Collection_Exception('Can not insert before a node that is not part of me');
        }

        if ($existingNode->getState() == Collection_LinkedList_Node::STATE_LOCKED) {
            return $this;
        }

        if ($existingNode === $candidateNode) {
            throw new Collection_Exception('Can not insert.Objects are identical ');
        }

        if ($candidateNode->isEmpty()) {
            throw new Collection_Exception('Can not insert an empty node');
        }

        if (!$candidateNode->belongsTo($this)) {
            $this->attachNode($candidateNode);
        }

        $existingNode->setPrevious($candidateNode);

        return $this;
    }

    public function insertAfterNode(
        Collection_LinkedList_Node $existingNode,
        Collection_LinkedList_Node $candidateNode
    ) {

        //TODO checking this first has some penalty issue when inserting
        if (!$existingNode->belongsTo($this)) {
            throw new Collection_Exception('Can not insert before a node that is not part of me');
        }

        if ($existingNode->getState() == Collection_LinkedList_Node::STATE_LOCKED) {
            return $this;
        }

        if ($existingNode === $candidateNode) {
            throw new Collection_Exception('Can not insert.Objects are identical ');
        }

        if (!$candidateNode->belongsTo($this)) {
            $this->attachNode($candidateNode);
        }

        $existingNode->setNext($candidateNode);

        return $this;
    }

    public function addNode(Collection_Node_Interface $node) {

        return $this->appendNode($node);

    }

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_Node_Collection
     */
    public function removeNode(Collection_Node_Interface $node) {

        if (!($node instanceof Collection_LinkedList_Node)) {
            throw new Collection_Exception(
                'parameter must be instance of Collection_LinkedList_Node. ' .
                'Thank PHP for this.'
            );
        }

        if (!$node->belongsTo($this)) {
            throw new Collection_Exception('Can not remove like this');
        }

        return $this->_remove($node);

    }

    public function attachNode(Collection_LinkedList_Node $node) {

        //primary check to be done here, speeds things a bit
        if ($node->belongsTo($this)) {
            return $this;
        }

        if ($node->isEmpty()) {
            throw new Collection_Exception('Can not attach empty node');
        }

        $next = $node->getNext();
        $previous = $node->getPrevious();

        //this prevents attaching random nodes, these checks return false
        //in case the node did not go first throug an insert method
        if(
            !empty($next) && !$next->belongsTo($this)
            || !empty($previous) && !$previous->belongsTo($this)
            || (empty($next) && empty($previous) && $this->_head !== $node)
        ) {
           throw new Collection_Exception('Can not attach. Node is not yet linked to my nodes');
        }

        $this->_index($node);
        $node->attach($this);

        return $node;
    }

    /**
     * @param Collection_LinkedList_Node $node
     * @param string $change
     * @return Collection_Node_Collection
     */
    public function onNodeChange(Collection_Node_Interface $node, $change='context') {

        if (!$node->belongsTo($this)) {
            throw new Collection_Exception('Node does not belong to me');
        }

        if ($change == 'context' && !$node->isEmpty()) {
            //unset previous object reference if any
            $objectHash = $this->nodes[$node->getHash()];
            unset($this->objects[$objectHash]);

            $objectHash  = $this->_getObjectHash($node->getContext());
            $this->objects[$objectHash] = $node;
            $this->nodes[$node->getHash()] = $objectHash;
            return $this;
        }

    }

    /**
     * @return Collection_LinkedList_Node
     */
    public function getHeadNode() {

        return $this->_head;

    }

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_LinkedList
     */
    public function setHeadNode(Collection_LinkedList_Node $node) {

        if (empty($this->_head)) {
            $this->_head = $node;
            $this->attachNode($node);
            return $this;
        }

        //no change needed
        if ($this->_head === $node) {
            return $this;
        }

        //update references
        $this->_head->setPrevious($node);
        $this->_head = $node;
        return $this;

    }

    /**
     * @return Collection_LinkedList_Node
     */
    public function getTailNode() {

        if (!empty($this->_tail)) {
            return $this->_tail;
        }

        return $this->_head;

    }

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_LinkedList
     */
    public function setTailNode(Collection_LinkedList_Node $node) {

        if (!$node->belongsTo($this)) {
            $this->appendNode($node);
        }

        //empty
        if (empty($this->_head)) {
            $this->_head = $node;
            return $this;
        }

        $tail = $this->getTailNode();

        //no change needed
        if ($tail === $node) {
            return $this;
        }

        //update references
        $tail->setNext($node);
        $this->_tail = $node;
        return $this;

    }

    /**
     * Garbage collection
     */
    public function ___gc() {
        $this->removeAll();
        $this->prototype = null;
    }

    public function __destruct() {
        $this->___gc();
    }

    /**
     * Adds the node in data and objects arrays
     * @param Collection_LinkedList_Node $node
     */
    protected function _index(Collection_LinkedList_Node $node) {

        $contextHash = Object_Type::hash($node->getContext());
        $this->objects[$contextHash] = $node;

    }

    /**
     * @param mixed $var
     * @return Collection_LinkedList_Node
     */
    protected function _toNode($var) {

        $hash = Object_Type::hash($var);
        $this->objects = (array) $this->objects;

        $exists = isset($this->objects[$hash]);
        if (!$exists) {
            $node = clone $this->prototype;
            $node->setContext($var);
        } else {
            $node = $this->objects[$hash];
        }

        return $node;
    }

    /**
     * Removes a node from the list, given its key
     * @param Collection_LinkedList_Node $node
     * @return Collection_LinkedList_Node
     */
    protected function _remove(Collection_LinkedList_Node $node) {

        $hash     = Object_Type::hash($node->getContext());
        unset($this->objects[$hash]);

        //update references
        if ($this->_head === $node) {
            $this->_head = null;
            $this->_head = $node->getNext();
        } else if ($this->_tail === $node) {
            $this->_tail = null;
            $this->_tail = $node->getPrevious();
        }

        $node->remove();
        return $node;
    }
}