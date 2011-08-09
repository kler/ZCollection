<?php
interface
    Collection_Node_Interface
extends
    Object_Hashable
{
    const STATE_LOCKED = 1;
    /**
     *
     * @return int
     */
    public function getState();

    /**
     *
     * @return Collection_Node_Collection
     */
    public function getCollection();

    /**
     *
     * @return boolean
     */
    public function isOrphan();

    /**
     * @param Collection_Node_Collection $collection
     * @return boolean
     */
    public function belongsTo(Collection_Node_Collection $collection);

    /**
     *
     * @param Collection_Node_Collection $collection
     * @return Collection_Node_Interface
     */
    public function attach(Collection_Node_Collection $collection);

    /**
     * @return Collection_Node_Collection
     */
    public function remove();

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