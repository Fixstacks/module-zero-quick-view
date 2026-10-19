<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\QuickView\ViewModel;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;
use Zero\Core\ViewModel\StickyAddToCart;

/**
 * Quick view: the cards' button (templates/button.phtml), the dialog
 * (templates/dialog.phtml) and the product in it (templates/content.phtml).
 */
class QuickView implements ArgumentInterface
{
    public const DIALOG_ID = 'zero-quickview';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly UrlInterface $url,
        private readonly ImageHelper $imageHelper,
        private readonly StickyAddToCart $addToCart
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('zero_quickview/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    public function contentUrl(Product $product): string
    {
        return $this->url->getUrl('quickview/product/view', ['id' => (int) $product->getId()]);
    }

    public function imageUrl(Product $product): string
    {
        return (string) $this->imageHelper->init($product, 'product_page_image_medium')->getUrl();
    }

    /** The price as product lists show it (special price, "As low as", tax display). */
    public function priceHtml(Product $product, LayoutInterface $layout): string
    {
        $render = $layout->getBlock('product.price.render.default')
            ?: $layout->createBlock(Render::class, 'product.price.render.default', ['data' => [
                'price_render_handle' => 'catalog_product_prices',
                'use_link_for_as_low_as' => true,
            ]]);

        return (string) $render->render(FinalPrice::PRICE_CODE, $product, [
            'include_container' => true,
            'display_minimal_price' => true,
            'zone' => Render::ZONE_ITEM_LIST,
        ]);
    }

    /** Short description as plain text, about two sentences. */
    public function summary(Product $product): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($product->getData('short_description') ?: $product->getData('description')))));
        if (mb_strlen($text) <= 280) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, mb_strrpos(mb_substr($text, 0, 280), ' ') ?: 280), " ,.;:") . '…';
    }

    /** Added from the dialog: a product with nothing to choose. */
    public function addsDirectly(Product $product): bool
    {
        return $product->isSaleable() && $this->addToCart->addsDirectly($product);
    }

    public function addUrl(Product $product): string
    {
        return $this->url->getUrl('checkout/cart/add', ['product' => (int) $product->getId()]);
    }
}
