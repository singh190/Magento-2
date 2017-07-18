<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18/7/17
 * Time: 5:57 PM
 */

namespace Singh\CacheManagement\Model\Logger\Handler;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Store\Model\ScopeInterface;

class Debug extends \Magento\Framework\Logger\Handler\Debug
{
    private $scopeConfig;

    public function __construct(
        DriverInterface $filesystem,
        ScopeConfigInterface $scopeConfig,
        $filePath = null)
    {
        parent::__construct($filesystem, $filePath);
        $this->scopeConfig = $scopeConfig;
    }

    public function isHandling(array $record)
    {
        return parent::isHandling($record)
        && $this->scopeConfig->getValue('cache_management/log_config/enable_debug_log', ScopeInterface::SCOPE_STORE);
    }
}
