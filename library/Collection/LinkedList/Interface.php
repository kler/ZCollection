<?php
interface Collection_LinkedList_Interface extends Collection_Interface{

    /**
     * @param Collection_LinkedList_Node $node
     * @return Collection_LinkedList_Node
     */
    public function attachNode(Collection_LinkedList_Node $node);

    /**
     * @param Collection_Node_Interface $node
     * @return Collection_LinkedList_Interface
     */
    public function appendNode(Collection_LinkedList_Node $node);

    /**
     * @param Collection_Node_Interface $node
     * @return Collection_LinkedList_Interface
     */
    public function prependNode(Collection_LinkedList_Node $node);

    /**
     *
     * @param Collection_LinkedList_Node $existing
     * @param Collection_LinkedList_Node $candidat
     * @return Collection_LinkedList_Interface
     */
    public function insertAfterNode(
        Collection_LinkedList_Node $existing,
        Collection_LinkedList_Node $candidat
    );

    /**
     * @param Collection_LinkedList_Node $existing
     * @param Collection_LinkedList_Node $candidat
     * @return Collection_LinkedList_Interface
     */
    public function insertBeforeNode(
        Collection_LinkedList_Node $existing,
        Collection_LinkedList_Node $candidat
    );

    /**
     * Adds an item at the end
     *
     * @param mixed $var
     * @return Collection_LinkedList_Interface
     */
    public function append($var);

    /**
     * Adds an item at the start
     *
     * @param mixed $var
     * @return Collection_LinkedList_Interface
     */
    public function prepend($var);

    /**
     * @param mixed $existing
     * @param mixed $candidate
     * @return Collection_LinkedList_Interface
     */
    public function insertBefore($existing, $candidate);

    /**
     * @param mixed $existing
     * @param mixed $candidate
     * @return Collection_LinkedList_Interface
     */
    public function insertAfter($existing, $candidate);

    /**
     *
     * @return Iterator
     */
    public function getIterator();

}