<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Controller\Adminhtml\Help;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\Resolver;

class Display extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $readInterface = $filesystem->getDirectoryRead(DirectoryList::APP);
        
        /** @var Resolver $localeResolver */
        $localeResolver = $this->_objectManager->get('Magento\Framework\Locale\Resolver');
        $locale = $localeResolver->getLocale();
        
        $defaultPath = 'code/Owebia/ShippingCore/view/doc_en_US.html';
        $localePath = str_replace('en_US', $locale, $defaultPath);
        
        $path = $readInterface->isFile($localePath) ? $localePath : $defaultPath;
        
        return $this->resultRawFactory->create()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', 'text/html; charset=UTF-8', true)
            ->setContents($readInterface->readFile($path));
    }
}
