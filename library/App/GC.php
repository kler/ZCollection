<?php
class App_GC {

    static protected $instance;

    /**
     * @return App_CG
     */
    static public function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    public function collect($var) {

        //as reported here http://php.net/manual/en/function.memory-get-usage.php,
        //this can trigger php's own garbage collection
        memory_get_usage(true);

        //destroy an array
        if (is_array($var)) {
            $nbrItems = count($var);
            while ($nbrItems) {
                $item = array_pop($var);
                self::collect($var);
                $nbrItems = $nbrItems -1;
            }
            $var = null;
            //force gc
            $cycles = gc_collect_cycles();
            unset($var);

            return null;
        }

        if (is_object($var)) {
            //call the object's destructor
            if (is_callable(array($var, '___gc'))) {
                $var->___gc();
            }
            //destroy the object in the original scope
            $var = null;
            //force gc
            $cycles = gc_collect_cycles();
            unset($var);

            return null;
        }
        unset($var);
        $var = null;
        return null;
    }

    public function collectCycles() {
        return gc_collect_cycles();
    }
}