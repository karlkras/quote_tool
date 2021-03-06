<?php

require_once (__DIR__ . "/../../../definitions.php");

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-06 at 18:11:31.
 */
class ClientPricingTableTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ClientPricingTable
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ClientPricingTable("client_brightcove", new PricingMySql());
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * @covers ClientPricingTable::supportsRate
     * @todo   Implement testSupportsRate().
     */
    public function testSupportsRate() {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
                true, $this->object->supportsRate()
        );
    }

    /**
     * @covers ClientPricingTable::supportsRushRate
     * @todo   Implement testSupportsRushRate().
     */
    public function testSupportsRushRate() {
        // Remove the following lines when you implement this test.
        $this->assertEquals(
                true, $this->object->supportsRushRate()
        );
    }
}
