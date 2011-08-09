<?php
require_once 'App/Test/Case.php';

class Collection_LinkedListTest_Context {

}

class Collection_LinkedList_Test extends Collection_LinkedList {
    static public $instances = 0;

    public function __construct(Collection_LinkedList_Node $prototype=null) {
        parent::__construct($prototype);
        self::$instances = self::$instances + 1;
    }

    public function __destruct() {
        parent::__destruct();

        self::$instances = self::$instances - 1;
    }
}

class Collection_LinkedListTest_Node extends Collection_LinkedList_Node{

    static public $instances = 0;

    public function __construct() {
        self::$instances = self::$instances + 1;
    }

    public function __destruct() {
        self::$instances = self::$instances - 1;
    }

    public function __clone() {
        self::$instances = self::$instances + 1;
    }
}

class Collection_LinkedListTest extends App_Test_Case {

    public function testInitialize() {
        $prototype = new Collection_LinkedList_Node();
        $collection = new Collection_LinkedList($prototype);
    }

    public function testAddNConsecutiveItems() {

        require_once 'Collection/LinkedList/Node.php';
        require_once 'Collection/LinkedList.php';
        $this->readMemoryUsage();
        $mem1 = $this->getLastMemoryReading();
        $N = 3;
        $this->_makeLinkedList($N, 'Collection_LinkedList_Test');
        $mem2 = $this->getLastMemoryReading();
        $this->readMemoryUsage();

        $diff = $this->getMemoryUsageAbsoluteDiff();
        $mem3 = $this->getLastMemoryReading();

        $this->_makeLinkedList($N, 'Collection_LinkedList_Test');
        $this->readMemoryUsage();
        $mem4 = $this->getLastMemoryReading();

        $hasCollections = Collection_LinkedList_Test::$instances == 0;
        $hasNodes       = Collection_LinkedListTest_Node::$instances == 0;

        $this->assertTrue($hasCollections && $hasNodes);
    }

    public function xtestCircularReferenceMemory() {

        $mock = PHPUnit_Framework_MockObject_Generator::generate(
          'Collection_LinkedList_Test',
          array('___gc')
        );
        eval($mock['code']);
        $className = $mock['mockClassName'];
        unset($mock);

        $this->readMemoryUsage();
        $mem1 = $this->getLastMemoryReading();
        $N = 3;
        $this->_makeLinkedList($N, $className);
        App_GC::getInstance()->collectCycles();
        $mem2 = $this->getLastMemoryReading();
        $this->readMemoryUsage();

        $diff = $this->getMemoryUsageAbsoluteDiff();
        $mem3 = $this->getLastMemoryReading();

        $hasCollections = Collection_LinkedList_Test::$instances > 0;
        $hasNodes       = Collection_LinkedListTest_Node::$instances > 0;

        $this->assertTrue($hasCollections && $hasNodes);
    }

    protected function _makeLinkedList($N, $collectionClass = 'Collection_LinkedList') {

        $prototype = new Collection_LinkedListTest_Node();
        $prototype->accepts('Collection_LinkedListTest_Context');
        $collection = new $collectionClass($prototype);
        $prototype = null;

        $head = new Collection_LinkedListTest_Context();
        $collection->append($head);
        $firstHead = $collection->getHeadNode();

        for($i = 1; $i < $N; $i++) {
            $tail = new Collection_LinkedListTest_Context();
            $collection->append($tail);
        }

        $this->readMemoryUsage();
        $diff = $this->getMemoryUsageAbsoluteDiff();

        $lastHead = $collection->getHeadNode();
        $this->assertTrue($firstHead === $lastHead);

        //unset all references
        $lastHead  = null;
        $firstHead = null;
        $tail      = null;
        //destroy this collection
        $collection = null;

        //App_GC::getInstance()->collect(&$collection);
        //force php to garbage collect
        App_GC::getInstance()->collectCycles();

    }
}