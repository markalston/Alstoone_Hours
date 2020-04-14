<?php
namespace Alstoone\Hours\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime;

class Payments implements ObserverInterface
{
    protected $_date;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,    
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Alstoone\Hours\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;        
        $this->_date = $date;    
       	$this->_logger = $logger;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/hours.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->testlog = $logger;
    }
    
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
	    
//        $this->testlog->info('In Observer');
//        $this->testlog->info($method->getCode());	

        if ($this->helperData->getGeneralConfig('enable')) {        
            $takePayments = ($this->helperData->isOpen() && $this->helperData->notHoliday());
            
            if (!$takePayments) {
                $result = $observer->getResult();	
                $result->setData('is_available', false);	
            }
        }
    }
}
