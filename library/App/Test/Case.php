<?php
include_once realpath(dirname(__FILE__).'/../../../tests/bootstrap.php');

class App_Test_Case extends PHPUnit_Framework_TestCase {

    protected $_memUsageReadings;

    static public function setUpBeforeClass() {

    }

    public function readMemoryUsage($real_allocated = false) {
        $this->_memUsageReadings = (array) $this->_memUsageReadings;
        array_push($this->_memUsageReadings, memory_get_usage($real_allocated));
        return $this;
    }

    public function getLastMemoryReading() {
        if (empty($this->_memUsageReadings)) {
            return null;
        }

        return $this->convertBytes(end($this->_memUsageReadings));
    }

    public function getMemoryUsageAbsoluteDiff() {

        if(empty($this->_memUsageReadings) || !isset($this->_memUsageReadings[1])){
            return null;
        }
        reset($this->_memUsageReadings);
        $top = $this->_memUsageReadings[0];
        $bottom = end($this->_memUsageReadings);

        return $this->convertBytes($bottom-$top);
    }

    public function convertBytes($size) {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}