<?php
class Satispay_PaymentProcessorTest_Test_Config_HelperTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testItHasDefaultHelperDefined()
    {
        $this->assertHelperAlias('satispay', 'Satispay_PaymentProcessor_Helper_Data');
    }
}
