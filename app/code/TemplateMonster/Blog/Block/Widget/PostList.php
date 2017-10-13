<?php

namespace TemplateMonster\Blog\Block\Widget;

use TemplateMonster\Blog\Model\ResourceModel\Post\Collection;
use TemplateMonster\Blog\Model\Url;

use TemplateMonster\Blog\Helper\Data as HelperData;

class PostList extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_postCollection;

    protected $_urlModel;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        Collection $postCollection,
        Url $url,
        HelperData $helper,
        array $data = []
    ) {
        $this->_urlModel = $url;
        $this->_helper = $helper;
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_postCollection = $postCollection;
    }

    public function getPosts()
    {
        $postAmount = (int) $this->getData('post_amount');
        $this->_postCollection
            ->addFieldToFilter('is_visible', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId());
        $this->_postCollection->getSelect()->order('creation_time', 'DESC')->limit($postAmount);
        return $this->_postCollection;
    }

    public function filterContent($data)
    {
        return $this->_filterProvider->getBlockFilter()->filter($data);
    }

    public function getPostUrl($post)
    {
        return $this->getUrl($this->_urlModel->getPostRoute($post));
    }

    public function getDateFormat()
    {
        return $this->_helper->getDataFormat();
    }
}
