<?php
abstract class
    Collection_Node_Tree
extends
    Collection_Node_Abstract {

    public function isRoot() {

        if (!$this->getParent()  && !$this->isOrphan()) {
            return true;
        }

        return false;

    }

    public function getLeft() {

        return $this->_left;

    }

    public function getRight() {

        return $this->_right;

    }

    public function getParent() {

        return $this->_parent;

    }

    abstract public function removeChild(Collection_Node_Tree $child);
}