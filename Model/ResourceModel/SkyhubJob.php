<?php


namespace Resultate\Skyhub\Model\ResourceModel;

class SkyhubJob extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('resultate_skyhub_skyhubjob', 'skyhubjob_id');
    }
}
