<?php
class
    Collection_BinaryTree
implements
    Collection_Node_Collection,
    Collection_BinaryTree_Interface
{
    /**
     * @var Collection_Node_Comparison
     */
    protected $_comparison;
    /**
     *
     * @var Collection_BinaryTree_Node
     */
    protected $_root;

    protected $_state;

    protected $_count = 0;

    //Collection_Interface methods
    public function isEmpty() {
        return $this->_count == 0;
    }

    public function count() {
        return $this->_count;
    }

    public function canAccept($var) {
        return true;
        throw new Collection_Exception('Needs implementation');
    }

    public function contains($var) {

        if ($this->isEmpty()) {
            return false;
        }

        $node = $this->_root->contains($var);

        if (empty($node)) {
            return false;
        }

        return true;
    }

    public function add($var) {

        if (!$this->canAccept($var) || empty($this->_comparison)) {
            throw new Collection_Exception('Can not add');
        }

        if (!($var instanceof Collection_BinaryTree_Node)) {
            $node = new Collection_BinaryTree_Node();
            $node->setContext($node);
        }
        return $this->addNode($node);

    }

    public function remove($var) {

        if ($this->isEmpty()) {
            return false;
        }

        $node = $this->_root->contains($var);

        if (empty($node)) {
            return false;
        }

        $node->remove();
        return true;

    }

    public function removeAll() {
        throw new Collection_Exception('Needs implementation');
    }

    //Collection_Node_Collection methods
    public function addNode (Collection_BinaryTree_Node $node)
    {
        if ($node->belongsTo($this)) {
            return $this;
        }

        if ($node->isEmpty() || empty($this->_comparison)) {
            throw new Collection_Exception('Can not insert');
        }

        if (!$node->isOrphan()) {
            $node->remove();
        }

        if ($this->isEmpty()) {
            $this->_root = $node;
            $node->attach($this);
        }

        $this->_root->insertNode($node);

        return $this;
    }

    public function removeNode(Collection_BinaryTree_Node $node) {

        if (!$node->belongsTo($this)) {
            return $this;
        }

        $node->remove();
    }

    public function onNodeChange(Collection_Node_Interface $node, $change='context') {

        if (!$node->belongsTo($this)) {
            throw new Collection_Exception('Wrong event');
        }
    }

    //OWN methods

    public function attachNode(Collection_BinaryTree_Node $node) {

        if (empty($this->_comparison)) {
            throw new Collection_Exception('Cannot attach node');
        }

        if ($node->belongsTo($this)) {
            return $this;
        }

        $right  = $node->getRight();
        $left   = $node->getLeft();
        $parent = $node->getParent();

        //this prevents attaching random nodes, these checks return false
        //in case the node did not go first throug an insert method
        if(
            !empty($right) && !$right->belongsTo($this)
            || !empty($left) && !$left->belongsTo($this)
            || !empty($parent) && !$parent->belongsTo($this)
            || (empty($left) && empty($right) && empty($parent) && $this->_root !== $node)
        ) {
           throw new Collection_Exception('Can not attach. Node is not yet linked to my nodes');
        }
        //attach the node, so it knows how to detach itself
        $node->attach($this);
        //set the comparison
        $node->setComparison($this->_comparison);

        return $node;
    }

    /**
     *
     * @return int
     */
    public function getState(){
        return $this->_state;
    }

    public function getRoot ()
    {
        return $this->_root;
    }

    public function setComparison (Collection_Node_Comparison $comparison)
    {
        if (!$this->isEmpty() && !empty($this->_comparison)) {
            $this->_comparision = $comparison;
            $this->_reorder();
        } else {
            $this->_comparison = $comparison;
        }
        return $this;
    }

    //Event Hooks
    public function onInsert(
        Collection_BinaryTree_Node $parent,
        Collection_BinaryTree_Node $child,
        $position = null
    ) {
        if (
            $position == Collection_BinaryTree_Interface::LEFT && $parent->getLeft() === $child
            || $position == Collection_BinaryTree_Interface::RIGHT && $parent->getRight() === $child
        ) {} else {
            throw new Collection_Exception('Wrong event');
        }

        if (
            !$child->belongsTo($this)
            || !$parent->belongsTo($this)
            || $child->getParent() !== $parent
        ) {
            throw new Collection_Exception('Wrong event');
        }

        $right  = $parent->getRight();
        $left   = $parent->getLeft();
        $parent = $parent->getParent();

            //this prevents attaching random nodes, these checks return false
        //in case the node did not go first throug an insert method
        if(
            !empty($right) && !$right->belongsTo($this)
            || !empty($left) && !$left->belongsTo($this)
            || empty($parent)
            || !$parent->belongsTo($this)
        ) {
           throw new Collection_Exception('Can not attach. Node is not yet linked to my nodes');
        }

        $this->_count = $this->_count + 1;
        //TODO determine if i need balancing
    }

    public function onRemove(
        Collection_BinaryTree_Node $parent,
        Collection_BinaryTree_Node $child,
        $position = null
    ) {
        if (
            $position != Collection_BinaryTree_Interface::LEFT
            && $position != Collection_BinaryTree_Interface::RIGHT
        ) {
            throw new Collection_Exception('Wrong event');
        }

        if (
            !$child->belongsTo($this)
            || !$parent->belongsTo($this)
        ) {
            throw new Collection_Exception('Wrong event');
        }

        $this->_count = $this->_count - 1;
    }

    protected function _reorder() {
        throw new Collection_Exception('Needs implementing');
    }
}