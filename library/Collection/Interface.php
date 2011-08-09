<?php
interface Collection_Interface {

    /**
     * Checks if collection is empty
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the number of objects in the collection
     *
     * @return int
     */
    public function count();

    /**
     * Checks if collection accepts objects of this type
     *
     * @param mixe $var
     * @return boolean
     */
    public function canAccept($var);

    /**
     * Checks if collection contains the parameter
     *
     * @param mixed $var
     * @return boolean
     */
    public function contains($var);

    /**
     * Add an object
     *
     * @param mixed $var
     */
    public function add($var);

    /**
     * Removes the object
     *
     * @param mixed $var
     * @return Collection_Interface
     */
    public function remove($var);

    /**
     * Removes all the objects in the collection
     *
     * @return Collection_Interface
     */
    public function removeAll();
}