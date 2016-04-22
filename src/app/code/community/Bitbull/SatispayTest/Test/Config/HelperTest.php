<?php
class Bitbull_SatispayTest_Test_Config_HelperTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testItHasDefaultHelperDefined()
    {
        $this->assertHelperAlias('satispay', 'Bitbull_Satispay_Helper_Data');
    }
}
