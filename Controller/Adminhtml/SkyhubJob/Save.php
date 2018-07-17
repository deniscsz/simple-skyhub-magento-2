<?php


namespace Resultate\Skyhub\Controller\Adminhtml\SkyhubJob;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('skyhubjob_id');
        
            $model = $this->_objectManager->create('Resultate\Skyhub\Model\SkyhubJob')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Skyhubjob no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Skyhubjob.'));
                $this->dataPersistor->clear('resultate_skyhub_skyhubjob');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['skyhubjob_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Skyhubjob.'));
            }
        
            $this->dataPersistor->set('resultate_skyhub_skyhubjob', $data);
            return $resultRedirect->setPath('*/*/edit', ['skyhubjob_id' => $this->getRequest()->getParam('skyhubjob_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
