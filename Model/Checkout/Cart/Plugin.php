<?php
namespace Alstoone\Hours\Model\Checkout\Cart;

use Magento\Framework\Exception\LocalizedException;

class Plugin
{

    public function __construct(
        \Psr\Log\LoggerInterface $logger,        
        \Alstoone\Hours\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
       	$this->_logger = $logger;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alstoone.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->alstoonelog = $logger;
    }

    public function beforeAddProduct(
       	\Magento\Checkout\Model\Cart $subject,
        $productInfo,
        $requestInfo = null
    ) {
        if ($this->helperData->getGeneralConfig('enable')) {
            $open = ($this->helperData->isOpen() && $this->helperData->notHoliday());
            if ($open) {	   
                return array($productInfo, $requestInfo);
            }
            if (!$this->helperData->notHoliday()) {
                throw new LocalizedException(__('Sorry but the restaurant is closed for the holiday.'));	
            } else {
                throw new LocalizedException(__('Sorry but the restaurant can only accept online orders from 15 min before open until 10 min before close. Please try again during business hours.'));
            }
        } 
    }

}
