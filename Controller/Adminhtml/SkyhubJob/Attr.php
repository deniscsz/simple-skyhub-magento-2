<?php


namespace Resultate\Skyhub\Controller\Adminhtml\SkyhubJob;

use Magento\Framework\Exception\LocalizedException;
use Resultate\Skyhub\Model\SkyhubJob;

class Attr extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Execute controller
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        try {

            $skyhubJob = $this->_objectManager->create('Resultate\Skyhub\Model\SkyhubJob');      
            $skyhubJob->setEntityType(SkyhubJob::ENTITY_TYPE_SYNC_ATTRIBUTES);
            $skyhubJob->setEntityId(0);
            $skyhubJob->save();

            $this->messageManager->addSuccessMessage(__('You saved the Skyhubjob.'));
            return $resultRedirect->setPath('*/*/');
            
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Skyhubjob.'));
        }
        
        return $resultRedirect->setPath('*/*/');
    }
}
