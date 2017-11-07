<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-27 at 18:38:18.
 */
class WorkUnitTest extends PHPUnit_Framework_TestCase {

    /**
     * @var WorkUnit
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new WorkUnit(WorkUnitType::enum()->hours, 200);
        $this->object->setBaseRatePerUnit(1.55);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers WorkUnit::get_unitType
     * @todo   Implement testGet_unitType().
     */
    public function testGet_unitType() {
        $this->assertEquals(
                "hours", $this->object->getUnitType()
        );
    }

    /**
     * @covers WorkUnit::get_unitCount
     * @todo   Implement testGet_unitCount().
     */
    public function testGet_unitCount() {
        $this->assertEquals(
                "200", $this->object->getUnitCount()
        );
    }

    /**
     * @covers WorkUnit::get_ratePerUnit
     * @todo   Implement testGet_ratePerUnit().
     */
    public function testGet_ratePerUnit() {
        $this->assertEquals(
                1.55, $this->object->getBaseRatePerUnit()
        );
    }

    /**
     * @covers WorkUnit::set_ratePerUnit
     * @todo   Implement testSet_ratePerUnit().
     */
    public function testSet_ratePerUnit() {
        $this->object->setBaseRatePerUnit(1.03);
        $this->assertEquals(
                1.03, $this->object->getBaseRatePerUnit()
        );
    }

    /**
     * @covers WorkUnit::set_unitCount
     * @todo   Implement testSet_unitCount().
     */
    public function testSet_unitCount() {
        $this->object->setUnitCount(3000);
        $this->assertEquals(
                3000, $this->object->getUnitCount()
        );
    }

    /**
     * @covers WorkUnit::set_unitType
     * @todo   Implement testSet_unitType().
     */
    public function testSet_unitType() {
        $this->object->setUnitType(WorkUnitType::enum()->pages);
        $this->assertEquals(
                "pages", $this->object->getUnitType()
        );
    }

}