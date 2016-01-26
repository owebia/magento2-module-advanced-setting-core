<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * @var string
     */
    protected $fileName = '/var/log/owebia_shippingcore.log';

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /**
     * @{inheritDoc}
     *
     * @param $record array
     * @return void
     */
    public function write(array $record)
    {
        $record['formatted'] = $record['message'];
        parent::write($record);
    }
}
