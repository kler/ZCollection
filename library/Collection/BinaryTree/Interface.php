<?php
interface Collection_BinaryTree_Interface{

    const STATE_INSERTING = 2;

    const STATE_DELETING = 4;

    const LEFT = 'left';

    const RIGHT = 'right';

    /**
     *
     * Enter description here ...
     * @param Collection_BinaryTree_Node $node
     * @return Collection_BinaryTree_Node
     */
    public function attachNode(Collection_BinaryTree_Node $node);

    public function getState();

    /**
     * @return Collection_BinaryTree_Node
     */
    public function getRoot();

    /**
     * Sets the comparison that all the nodes will use
     *
     * @param Collection_Node_Comparison $comparison
     * @return Collection_BinaryTree_Interface
     */
    public function setComparison(Collection_Node_Comparison $comparison);

    //EVENT HOOKS

    public function onInsert(
        Collection_BinaryTree_Node $parent,
        Collection_BinaryTree_Node $child,
        $position = null
    );
}