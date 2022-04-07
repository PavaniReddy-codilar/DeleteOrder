<?php

namespace Codilar\DeleteOrder\Controller\Adminhtml\MassAction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\UrlInterface;

use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class MassDelete
 */
class CustomAction extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;
    protected $resultFactory;
    private $url;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
      //  resultFactory  $resultFactory,
        UrlInterface $url,
        OrderManagementInterface $orderManagement
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
        //$this->resultFactory=$resultFactory;
    }

    /**
     * Hold selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
   
    {
        $redirectResponse = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $countDeleteOrder = 0;
        $model = $this->_objectManager->create('Magento\Sales\Model\Order');
        foreach ($collection->getItems() as $order) {
            if (!$order->getEntityId()) {
                continue;
            }
            $loadedOrder = $model->load($order->getEntityId());
            $loadedOrder->delete();
            $countDeleteOrder++;
        }
        $countNonDeleteOrder = $collection->count() - $countDeleteOrder;

        if ($countNonDeleteOrder && $countDeleteOrder) {
            $this->messageManager->addError(__('%1 order(s) were not deleted.', $countNonDeleteOrder));
        } elseif ($countNonDeleteOrder) {
            $this->messageManager->addError(__('No order(s) were deleted.'));
        }

        if ($countDeleteOrder) {
            $this->messageManager->addSuccess(__('You have deleted %1 order(s).', $countDeleteOrder));
        }
        $redirectResponse->setUrl($this->_redirect->getRefererUrl());
            return $redirectResponse;
     //   $resultRedirect = $this->resultRedirectFactory->create();
       // $resultRedirect->setPath($this->getComponentRefererUrl());
      //  return $resultRedirect;
      

    }
}