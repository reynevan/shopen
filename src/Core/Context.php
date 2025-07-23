<?php

namespace Shopen\Core;

use Shopen\Models\Category\Category;
use Shopen\Models\Order\Order;
use Shopen\Models\Product\Product;

class Context
{
    protected ?Product $product = null;
    protected ?Category $category = null;
    protected ?Order $order = null;
    protected bool $isAdmin = false;

    public function getCurrentProduct(): ?Product
    {
        return $this->product;
    }

    public function setCurrentProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getCurrentCategory(): ?Category
    {
        return $this->category;
    }

    public function setCurrentCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function setCurrentOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getCurrentOrder(): ?Order
    {
        return $this->order;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}