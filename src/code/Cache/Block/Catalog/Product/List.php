<?php

/**
 * Use this for granular product list cache
 *
 * @package Made_Cache
 * @author info@madepeople.se
 * @copyright Copyright (c) 2014 Made People AB. (http://www.madepeople.se/)
 */
class Made_Cache_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    const DEFAULT_PRODUCT_TEMPLATE = 'catalog/product/list/product.phtml';

    /**
     * Fixes a core bug that likes to believe a category object is an integer
     *
     * @param $categoryId
     */
    public function setCategoryId($categoryId)
    {
        if (is_object($categoryId)) {
            $categoryId = $categoryId->getId();
        }
        return parent::setCategoryId($categoryId);
    }

    /**
     * Return the default product template unless specified differently
     *
     * @return mixed
     */
    public function getProductTemplate()
    {
        if (!$this->hasData('product_template')) {
            $this->setData('product_template', self::DEFAULT_PRODUCT_TEMPLATE);
        }
        return $this->getData('product_template');
    }

    /**
     * For granular caching of product list blocks. Requires the markup
     * of a single product to be broken out of list.phtml into
     * catalog/product/list/product.phtml
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductHtml($product)
    {
        // Prevent crash within catalog_product_view
        if (($viewedProduct = Mage::registry('product')) !== null) {
            Mage::unregister('product');
        }
        $block = $this->getLayout()
                ->createBlock('cache/catalog_product_list_product')
                ->setCacheLifetime($this->getCacheLifetime())
                ->setTemplate($this->getProductTemplate())
                ->setProduct($product);

        $html = $block->toHtml();
        Mage::register('product', $viewedProduct);
        return $html;
    }
}
