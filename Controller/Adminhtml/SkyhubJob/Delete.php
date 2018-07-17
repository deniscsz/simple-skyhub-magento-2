<?php


namespace Resultate\Skyhub\Controller\Adminhtml\SkyhubJob;

class Delete extends \Resultate\Skyhub\Controller\Adminhtml\SkyhubJob
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('skyhubjob_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Resultate\Skyhub\Model\SkyhubJob');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Skyhubjob.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['skyhubjob_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Skyhubjob to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
