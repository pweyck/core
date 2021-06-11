<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Event;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\ShopwareSalesChannelEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeCartSavedEvent extends Event implements ShopwareSalesChannelEvent
{
    /**
     * @var SalesChannelContext
     */
    protected $context;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var bool
     */
    protected $save = false;

    public function __construct(SalesChannelContext $context, Cart $cart)
    {
        $this->context = $context;
        $this->cart = $cart;
    }

    public function getContext(): Context
    {
        return $this->context->getContext();
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function savesCart(): bool
    {
        return $this->save;
    }

    public function needsSaving(): void
    {
        $this->save = true;

        $this->stopPropagation();
    }
}
