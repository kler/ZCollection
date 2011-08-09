<?php
interface Collection_Node_Collection extends Collection_Interface {

    /**
     * @param Collection_Node_Interface $node
     * @return Collection_Node_Collection
     */
    public function addNode(Collection_Node_Interface $node);

    /**
     * @param Collection_Node_Interface $node
     * @return Collection_Node_Collection
     */
    public function removeNode(Collection_Node_Interface $node);

    /**
     * @param Collection_Node_Interface $node
     * @param string $change
     * @return Collection_Node_Collection
     */
    public function onNodeChange(Collection_Node_Interface $node, $change='context');
}