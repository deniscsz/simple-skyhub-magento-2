<?php


namespace Resultate\Skyhub\Controller\Adminhtml\SkyhubJob;

use Magento\Framework\Exception\LocalizedException;
use Resultate\Skyhub\Model\SkyhubJob;

class ProductAdd extends \Magento\Catalog\Controller\Adminhtml\Product
{

     /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute controller
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('catalog/product/index');

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        try {
            $productsSaved = 0;
            foreach ($collection->getAllIds() as $productId) {

                $skyhubJob = $this->_objectManager->create('Resultate\Skyhub\Model\SkyhubJob');      
                $skyhubJob->setEntityType(SkyhubJob::ENTITY_TYPE_CATALOG_PRODUCT_SAVE);
                $skyhubJob->setEntityId($productId);
                $skyhubJob->save();

                $productsSaved++;
            }

            if ($productsSaved) {
                $this->messageManager->addSuccess(__('A total of %1 record(s) were save.', $productsSaved));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Skyhubjob.'));
        }
        
        return $resultRedirect;
    }
}
