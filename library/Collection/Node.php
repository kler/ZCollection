<?php
class Collection_Node {

    /**
     *
     * @return int
     */
    public function getState();

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param Collection_Node_Collection $collection
     * @return boolean
     */
    public function belongsTo(Collection_Node_Collection $collection);

    /**
     * @param string|array $type
     */
    public function accepts($type);

    /**
     * @param mixed $var
     * @return boolean
     */
    public function canAccept($var);

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @param mixed $var
     * @return Collection_Node_Interface
     */
    public function setContext($var);

    /**
     * @return mixed
     */
    public function getContext();
}