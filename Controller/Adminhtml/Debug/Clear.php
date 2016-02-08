<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Owebia\ShippingCore\Controller\Adminhtml\Debug;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Clear extends \Magento\Backend\App\Action
{

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $writeInterface = $filesystem->getDirectoryWrite(DirectoryList::LOG);

        $writeInterface->writeFile('owebia_shippingcore.log', '');

        return $this->_redirect('owebia-shippingcore/debug/index');
    }
}
