<?php

class Bitbull_SatispayTest_Test_Model_PaymentTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Helper instance
     *
     * @var Bitbull_Satispay_Helper_Data
     */
    private $helper;
    
    /**
     * Payment instance
     *
     * @var Bitbull_Satispay_Model_Payment
     */
    private $payment;
    
    /**
     * Session instance
     *
     * @var Bitbull_Satispay_Model_Session
     */
    private $session;
    
    protected function setUp()
    {
        // Helper
        $this->helper = Mage::helper('satispay');
        
        // Payment object
        $order = new Mage_Sales_Model_Order();
        
        $info = new Mage_Sales_Model_Order_Payment();
        $info->setOrder($order);
        
        $this->payment = new Bitbull_Satispay_Model_Payment();
        $this->payment->setInfoInstance($info);
        
        // Core session
        $session = $this->getModelMockBuilder('core/session')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        
        $this->replaceByMock('singleton', 'core/session', $session);
        
        // Satispay session
        $this->session = $this->getModelMockBuilder('satispay/session')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        
        $this->replaceByMock('singleton', 'satispay/session', $this->session);
    }
    
    public function testExtendsPaymentMethodAbstractCoreClass()
    {
        $this->assertTrue($this->payment instanceof Mage_Payment_Model_Method_Abstract);
    }
    
    public function testSessionClassInstance()
    {
        $this->assertTrue($this->session instanceof Bitbull_Satispay_Model_Session);
    }
    
    public function testPaymentInitialization()
    {
        // Random payment info
        $incrementId = uniqid();
        $amount = rand(0.01, 9999);
        $currency = array_rand(array('EUR', 'USD', 'GBP', 'CHF'));
        
        $this->payment->getInfoInstance()->getOrder()->setIncrementId($incrementId);
        $this->payment->getInfoInstance()->getOrder()->setQuoteBaseGrandTotal($amount);
        $this->payment->getInfoInstance()->getOrder()->setBaseCurrencyCode($currency);
        
        // Initialize payment
        $this->payment->initialize(null, null);
        
        // Verify that values are assigned to session as expected
        $this->assertSame($this->session->getOrderId(), $incrementId);
        $this->assertSame($this->session->getAmount(), round($amount * 100));
        $this->assertNotNull($this->session->getCurrency(), $currency);
    }
    
    /**
     * Test an actual payment creation
     * 
     * @loadFixture
     */
    public function testPaymentCreation()
    {
        // Test user creation
        $client = $this->helper->getClient();
        $user = $client->userCreate('+393492876938');

        $this->assertNotNull($user);
        $this->assertObjectHasAttribute('id', $user);
        
        // Test charge creation
        $orderId = uniqid();
        $amount = rand(0.01, 9999);
        $currency = 'EUR';

        $charge = $client->chargeCreate(
            $orderId,
            $user->id,
            $currency,
            round($amount * 100)
        );

        $this->assertNotNull($charge);
        $this->assertObjectHasAttribute('id', $charge);
    }
}
