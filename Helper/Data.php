<?php

namespace Alstoone\Hours\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime;

class Data extends AbstractHelper
{
    
    protected $_date;
    const XML_PATH_HOURS = 'hours/';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,        
        \Psr\Log\LoggerInterface $logger,        
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    ) {
        $this->_date = $date;
       	$this->_logger = $logger;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alstoone.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->alstoonelog = $logger;
        parent::__construct($context);        
    }
    
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HOURS .'general/'. $code, $storeId);
    }


    public function isOpen() {
        $status = FALSE;
        $currentTime = $this->_date->date()->format('H:i A');

	    $hours = $this->getGeneralConfig('openhours');
        
        $hoursarray = json_decode($hours);
        

        foreach ($hoursarray as $dayhours) {
            // loop through time ranges for current day                
            if (date('D') == $dayhours->day) {
                // $this->alstoonelog->info('Day Matches');
                // $this->alstoonelog->info(date('D'));
                // $this->alstoonelog->info($dayhours->day);                                                
                // $this->alstoonelog->info($dayhours->from_hour);
                // $this->alstoonelog->info($dayhours->to_hour);
                // create time objects from start/end times and format as string (24hr AM/PM)                
                $startTime = \DateTime::createFromFormat('h:i A', $dayhours->from_hour)->format('H:i A');
                $endTime = \DateTime::createFromFormat('h:i A', $dayhours->to_hour)->format('H:i A');
                // check if current time is within the range                
                if (($startTime < $currentTime) && ($currentTime < $endTime)) {
                    // $this->alstoonelog->info('We are open');                                    
                    $status = TRUE;
                    break;
                }
            }
        }
        return $status;
    }

    public function notHoliday() {
        $status = TRUE;    
        $currentDay = $this->_date->date()->format('Y-m-d');
	    $closeddays = $this->getGeneralConfig('closeddays');
        $closedarray = json_decode($closeddays);
        // $this->alstoonelog->info('Current day: ' . $currentDay);        
        // $this->alstoonelog->info(print_r($closedarray,1));
        if (!empty($closedarray)) {
            foreach ($closedarray as $day) {
                // loop through searching for current day                
                if ($currentDay == $day->day) {
                    // $this->alstoonelog->info('We are closed');                        
                    $status = FALSE;
                    break;
                }
            }
        }
        return $status;
    }
}