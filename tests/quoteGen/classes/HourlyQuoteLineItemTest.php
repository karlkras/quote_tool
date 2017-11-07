<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-28 at 22:55:55.
 */
class HourlyQuoteLineItemTest extends PHPUnit_Framework_TestCase {

    /**
     * @var QuoteLineItem
     */
    protected $object;

    /**
     * Providing a basic Online review hourly quote line item for each test.
     */
    protected function setUp() {
        
        $this->object = $this->createHourlyTask();
        
        $this->object->setDefaultRushFeePercentage(0.25);
        $this->object->setBaseRatePerUnit(30);
        $this->object->setMarginPercentage(0.40);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    /**
     * @covers QuoteLineItem::getActualSellPricePerUnit
     * @covers QuoteLineItem::isCustomPricePerUnit
     * @covers QuoteLineItem::setCustomRatePerUnit
     */    
    public function testShouldSuccessfullyDisplayActualSellPricePerUnit() {
        $this->assertEquals(
            50.000, $this->object->getActualSellPricePerUnit()
        );
        
        $this->assertEquals(
            false, $this->object->isCustomPricePerUnit()
        );

//        $this->object->setCustomRatePerUnit(60.000);
//        $this->assertEquals(
//            60.000, $this->object->getActualSellPricePerUnit()
//        );
//        
//        $this->assertEquals(
//            true, $this->object->isCustomPricePerUnit()
//        );
    }
    
    /**
     * @covers QuoteLineItem::getBaseRatePerUnit
     * @covers QuoteLineItem::setBaseRatePerUnit
     */
    public function testShouldSuccessfullyReportLineItemBaseRatePerUnit() {
        $this->assertEquals(
            30, $this->object->getBaseRatePerUnit()
        );
        $this->object->setBaseRatePerUnit(60.00);
        $this->assertEquals(
                60.00, $this->object->getBaseRatePerUnit()
        );
    }    

    /**
     * @covers QuoteLineItem::setCustomRatePerUnit
     * @covers QuoteLineItem::getCustomRatePerUnit
     */
    public function testShouldSuccessfullyReportLineItemCustomRatePerUnit() {
        $this->object->setCustomRatePerUnit(45.00);

        $this->assertEquals(
               45.00, $this->object->getCustomRatePerUnit()
        );
    }

    /**
     * @covers QuoteLineItem::getWorkUnitType
     */
    public function testShouldSuccessfullyReportLineItemUnitType() {
        $this->assertEquals(
                "hours", $this->object->getWorkUnitType()
        );
    }

    /**
     * @covers QuoteLineItem::getRushFee
     */
//    public function testShouldSuccessfullyComputeLineItemRushFee() {
//        $this->assertEquals(
//                20.0, $this->object->getRushFee()
//        );
//    }

    /**
     * @covers QuoteLineItem::getFormattedCostTotal
     */
    public function testShouldSuccessfullyProvideLineItemCostTotalInUSCurrency() {
        $this->assertEquals(
                "$30.00", $this->object->getFormattedCostTotal()
        );
    }

    /**
     * @covers QuoteLineItem::getFormattedActualSellPriceTotal
     */
    public function testShouldSuccessfullyProvideLineItemActualSellPriceTotalInUSCurrency() {
        //$this->object->setCustomRatePerUnit(60.000);
        $this->assertEquals(
                "$50.00", $this->object->getFormattedActualSellPriceTotal()
        );
    }

    /**
     * @covers QuoteLineItem::getCalculatedSellPricePerUnit
     */
    public function testShouldSuccessfullyReportCalculatedSellPricePerUnit() {
        $this->assertEquals(
                50, $this->object->getCalculatedSellPricePerUnit()
        );
    }

    /**
     * @covers QuoteLineItem::getActualSellPricePerUnit
     * @covers QuoteLineItem::setCustomRatePerUnit
     */
    public function testShouldSuccessfullyReportActualSellPricePerUnit() {
        $this->assertEquals(
                50.00, $this->object->getActualSellPricePerUnit()
        );

        $this->object->setCustomRatePerUnit(60.00);

        $this->assertEquals(
                60.00, $this->object->getActualSellPricePerUnit()
        );
    }
    
    
    /**
     * @covers QuoteLineItem::getActualGrossMarginPercentage
     * @covers QuoteLineItem::setCustomRatePerUnit
     */
//    public function testShouldProperlyReportActualGrossMarginPercentage() {
//        $this->object->setCustomRatePerUnit(60.00);
//        $this->assertEquals(
//                33.3, $this->object->getActualGrossMarginPercentage()
//        );
//        $this->object->setCustomRatePerUnit(70.00);
//        $this->assertEquals(
//                42.9, $this->object->getActualGrossMarginPercentage()
//        );
//    }
    
    protected function createHourlyTask() {
        
        $enum = QuoteLineItemEnum::enum()->ICR;
        $newTask = new LinguistQuoteLineItem(new WorkUnit(WorkUnitType::enum()->hours, 1),new QuoteInfoClass());
        return $this->populateTask($enum, $newTask);
    }
    
    protected function populateTask(QuoteLineItemEnum $theEnum, ILinguistQuoteItem $item) {
        //$item->set(QuoteConstants::getRatePricingDBLookupRef($theEnum->getName()));
        $item->setDescription(QuoteConstants::getDescription($theEnum->getName()));
        $item->setStandardRateKey(QuoteConstants::getStandardRateKey($theEnum->getName()));
        $item->setDisplayName(QuoteConstants::getDisplayName($theEnum->getName()));
        return $item;
    }
    
}




class QuoteInfoClass implements ILinguistQuoteItem {
    public function getCategory() {
        return "ICR";
    }

    public function getId() {
        return "2222333";
    }

    public function getName() {
        return "ICR";
    }

    public function getSourceLang() {
        return "Englisn (US)";
    }

    public function getTargetLang() {
        return "French (France)";
    }

    public function getType() {
        return "ICR";
    }

    public function setCategory($cat) {
        
    }

    public function setId($id) {
        
    }

    public function setName($name) {
        
    }

    public function setType($type) {
        
    }

    public function getTargetLangCount() {
        
    }

    public function isDistributed() {
        
    }

}