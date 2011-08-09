<?php
class
    Collection_BinaryTree_Node
extends
    Collection_Node_Tree
{

    /**
     * @var Collection_BinaryTree_Node
     */
    protected $_left;

    /**
     * @var Collection_BinaryTree_Node
     */
    protected $_right;

    /**
     * @var Collection_BinaryTree_Node
     */
    protected $_parent;

    /**
     * @var Collection_Node_Comparison
     */
    protected $_comparison;

    protected $_equals = array();

    /**
     *
     *
     * @param Collection_Node_Comparison $comparison
     * @throws Collection_Exception
     */
    public function setComparison (Collection_Node_Comparison $comparison)
    {

        if ($this->isOrphan()) {
            $this->_comparison = $comparison;
            return $this;
        }

        throw new Collection_Exception('Can not set comparator on an attached node');
    }

    /**
     * Searches for a value in this subtree
     *
     * @param mixed $candidate
     * @param Collection_Node_Comparison $comparison
     * @throws Collection_Exception
     * @return boolean
     */
    public function contains($candidate) {

        if ($this->isEmpty()) {
            throw new Collection_Exception('Can not lookup node');
        }

        return $this->_contains($candidate);
    }

    public function insert(Collection_BinaryTree_Node $candidate) {

        if ($this->isEmpty() || $this->isOrphan() || $candidate->isEmpty()) {
            throw new Collection_Exception('Can not insert node');
        }

        $this->_insert($candidate);

        return $this;

    }

    public function attach(Collection_Node_Collection $collection) {

        if (!($collection instanceof Collection_BinaryTree)) {
            throw new Collection_Exception('Invalid parameter type');
        }

        parent::attach($collection);

        return $this;

    }

    public function remove() {

        if ($this->isOrphan() || $this->getState() == Collection_Node_Interface::STATE_LOCKED) {
            return $this;
        }

        //enter in workflow, de-reference collection etc
        parent::remove();

        return $this;
    }

    /**
     * Handles detaching of the node
     *
     * @param Collection_BinaryTree_Node $node
     */
    protected function _removweChild(
        Collection_Node_Tree $child,
        Collection_Node_Tree $replacement = null
    ) {

        if ($child === $this->_left) {
            $this->_left = replacement;
        } else if ($child === $this->_right) {
            $this->_right = replacement;
        } else {
            throw new COllection_Exception('Can not detach a node that is not a child');
        }

        $child->_parent = null;
        if ($replacement) {
            $replacement->_parent = $this;
        }

        $this->onAfterRemoveChild($child, Collection_BinaryTree_Interface::RIGHT, $replacement);
        return $this;

    }

    //ROTATIONS
    public function rotateLeft() {

        if (!$this->_right || $this->isOrphan() || $this->isEmpty()) {
            return $this;
        }

        $wasRoot = $this->isRoot();
        $this->_rotateLeft();
        if ($wasRoot) {
            $this->getCollection()->onRootChange($this->_parent);
        }

        return $this;
    }

    public function rotateRight() {

        if (!$this->_left || $this->isOrphan() || $this->isEmpty()) {
            return $this;
        }

        $wasRoot = $this->isRoot();
        $this->_rotateRight();
        if ($wasRoot) {
            $this->getCollection()->onRootChange($this->_parent);
        }

        return $this;
    }

    //EVENT HOOKS
    protected function onAfterRemoveChild(Collection_Node_Tree $node, $position) {

    }


    protected function _contains($candidate) {

        $result = $this->_comparison->compare($this->getContext(), $candidate);

        if ($result == 0) {
            return $this;
        }

        //greater and it has a right node
        if ($result > 0 && $this->getRight()) {
            return $this->getRight()->_contains($candidate);
        }

        //greater and it doesnt have a right node
        if ($result > 0 && !$this->getRight()) {
            return null;
        }

        //less and it has a left node
        if ($result < 0 && $this->getLeft()) {
            return $this->getLeft()->_contains($candidate);
        }

        //less and it doesnt have a left node
        if ($result > 0 && !$this->getLeft()) {
            return null;
        }
    }

    protected function _insert(Collection_BinaryTree_Node $candidate) {

        $result = $this->_comparison->compare($this->getContext(), $candidate->getContext());

        //greater and it has a right node
        if ($result > 0 && $this->getRight()) {
            return $this->getRight()->_insertNode($candidate);
        }

        //less and it has a left node
        if ($result < 0 && $this->getLeft()) {
            return $this->getLeft()->_insertNode($candidate);
        }

        //we can insert now, but first detach if this is the case
        if (!$candidate->isOrphan()) {
            $candidate->remove();
        }

        //handle equality
        if ($result == 0) {
            return $this->_insertEqualNode($candidate);
        }

        //greater and it doesnt have a right node
        if ($result > 0 && !$this->getRight()) {
            $this->_right = $candidate;
            $candidate->_parent = $this;
            $this->getCollection()->onInserts(
                $this,
                $candidate,
                Collection_BinaryTree_Interface::RIGHT
            );
            return $this;
        }

        //less and it doesnt have a left node
        if ($result > 0 && !$this->getLeft()) {
            $this->_left = $candidate;
            $candidate->_parent = $this;
            $this->getCollection()->onInsert(
                $this,
                $candidate,
                Collection_BinaryTree_Interface::LEFT
            );
            return $this;
        }

    }

    /**
     * Handles insertion of equal value node
     *
     * @param Collection_BinaryTree_Node $candidate
     */
    protected function _insertEqual(Collection_BinaryTree_Node $candidate) {

        $context = $candidate->getContext();
        //maybe hash ?
        $this->_equals[] = $context;

        return $this;
    }

    protected function _remove() {

        if (!$this->_left && !$this->_right) {
            $this->_parent->_removeChild($this);
        } else if ($this->_left && $this->_right) {
            $left = $this->_left;
            $right = $this->_right;
            $this->_left = null;
            $this->_right = null;

        } else if ($this->_left) {
            $left = $this->_left;
            $this->_left = null;
            $left->_parent = $this->_parent;
            $this->_parent->_removeChild($this, $left);
            $left = null;
        } else if ($this->_right) {
            $right = $this->_right;
            $this->_right = null;
            $right->_parent = $this->_parent;
            $this->_parent->_removeChild($this, $right);
            $right = null;
        }

        //de-reference the collection
        parent::_remove();

    }

    /**
     * Performs the left rotation
     */
    protected function _rotateLeft() {

        $right = $this->getRight();
        $parent = $this->getParent();

        //unset all references in this
        $this->_right = null;
        $this->_parent = null;

        //set parent
        $right->_parent = $parent;
        $this->_parent = $right;

        //old right takes the position of this in the parent
        if ($parent->_right === $this) {
            $parent->_right = null;
            $parent->_right = $right;
        } else {
            $parent->_left = null;
            $parent->_left = $right;
        }

        //set new left/right relation between this and the old right
        $this->_right = $right->_left;
        $right->_left = $this;

        //TODO throw events;

    }

    /**
     * Performs the right rotation
     */
    protected function _rotateRight() {

        $left = $this->getLeft();
        $parent = $this->getParent();

        //unset all references in this
        $this->_left = null;
        $this->_parent = null;

        //set parent
        $left->_parent = $parent;
        $this->_parent = $left;

        //old left takes the position of this in the parent
        if ($parent->_right === $this) {
            $parent->_right = null;
            $parent->_right = $left;
        } else {
            $parent->_left = null;
            $parent->_left = $left;
        }

        //set new left/right relation between this and the old right
        $this->_left = $left->_right;
        $left->_right = $this;

        //TODO throw events
    }

}