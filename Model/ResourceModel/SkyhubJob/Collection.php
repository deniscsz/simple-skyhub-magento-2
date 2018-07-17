<?php


namespace Resultate\Skyhub\Model\ResourceModel\SkyhubJob;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Resultate\Skyhub\Model\SkyhubJob',
            'Resultate\Skyhub\Model\ResourceModel\SkyhubJob'
        );
    }
}
