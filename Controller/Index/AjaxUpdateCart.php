<?php
/**
 * @category   Emarsys
 * @package    Emarsys_Emarsys
 * @copyright  Copyright (c) 2017 Emarsys. (http://www.emarsys.net/)
 */
namespace Emarsys\Emarsys\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Emarsys\Emarsys\Model\Logs;
use Magento\Store\Model\StoreManagerInterface;

class AjaxUpdateCart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Logs
     */
    protected $emarsysLogs;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AjaxUpdate constructor.
     * @param Context $context
     * @param Data $jsonHelper
     * @param Logs $emarsysLogs
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        Logs $emarsysLogs,
        StoreManagerInterface $storeManager
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->emarsysLogs = $emarsysLogs;
        parent::__construct($context);
    }

    /**
     * Ajax Action
     */
    public function execute()
    {
        $result = [];
        $result['content'] = '';
        $result['status'] = 0;
        try {
            $this->_view->loadLayout();
            $layout = $this->_view->getLayout();

            $parentBlock = $layout->createBlock('Emarsys\Emarsys\Block\JavascriptTracking',
                '',
                ['full_action_name' => $this->getRequest()->getParam('full_action_name')]
            )->setTemplate('Emarsys_Emarsys::emarsys/cart.phtml');

            $result['content'] = $parentBlock->toHtml();
            $result['status'] = 1;
        } catch (\Exception $e) {
            $this->emarsysLogs->addErrorLog(
                $e->getMessage(),
                $this->storeManager->getStore()->getId(),
                'AjaxUpdateCart::execute()'
            );
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
    }
}

