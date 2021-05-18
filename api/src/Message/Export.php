<?php


namespace App\Message;


use App\Entity\Product;
use JetBrains\PhpStorm\Pure;

class Export
{
    /**
     * @var Product[]
     */
    private array $products = [];

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param Product $products
     */
    public function addProduct(Product $products): void
    {
        $this->products[] = $products;
    }

    public function getProductsIds(): array
    {
        $result = [];
        foreach ($this->products as $product)
            $result[] = $product->getId();

        return $result;

    }


}
