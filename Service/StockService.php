<?php

namespace Flower\StockBundle\Service;

use Flower\ModelBundle\Entity\Stock\Product;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class StockService implements ContainerAwareInterface
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Product $product
     * @param int $quantity
     * @param bool $affectRawMaterialStock
     */
    public function increaseProduct(Product $product, $quantity = 1, $affectRawMaterialStock = true)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* product stock */
        $previousStock = $product->getStock();
        $product->setStock($previousStock + $quantity);

        if ($affectRawMaterialStock) {
            foreach ($product->getRawMaterials() as $productRawMaterial) {
                $rawMaterial = $productRawMaterial->getRawMaterial();
                $previousRMStock = $rawMaterial->getStock();

                $rawMaterial->setStock($previousRMStock - $productRawMaterial->getQuantity());
            }

        }

        $em->flush();
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    public function decreaseProduct(Product $product, $quantity = 1)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* product stock */
        $previousStock = $product->getStock();
        $product->setStock($previousStock - $quantity);

        $em->flush();
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}