<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18/7/17
 * Time: 5:57 PM
 */

namespace Corra\CacheManagement\Model\Logger\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Logger\Handler\Exception;

class Report extends \Magento\Support\Model\Logger\Handler\Report
{
    private $scopeConfig;

    public function __construct(DriverInterface $filesystem,
                                Exception $exceptionHandler,
                                ScopeConfigInterface $scopeConfig,
                                $filePath = null)
    {
        parent::__construct($filesystem, $exceptionHandler, $filePath);
        $this->scopeConfig = $scopeConfig;
    }

    public function isHandling(array $record)
    {
        return parent::isHandling($record)
        && $this->scopeConfig->getValue('cache_management/log_config/enable_report_log', ScopeInterface::SCOPE_STORE);
    }
}
