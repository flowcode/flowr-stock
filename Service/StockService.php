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

                $rawMaterial->setStock($previousRMStock - ($quantity * $productRawMaterial->getQuantity()));
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
     * Upload user image.
     *
     * @param Product $entity
     * @return Product
     */
    public function uploadImage(Product $entity)
    {

        /* the file property can be empty if the field is not required */
        if (null === $entity->getFile()) {
            return $entity;
        }

        $uploadBaseDir = $this->container->getParameter("uploads_base_dir");
        $uploadDir = $this->container->getParameter("product_dir");

        /* set the path property to the filename where you've saved the file */
        $filename = $entity->getFile()->getClientOriginalName();
        $extension = $entity->getFile()->getClientOriginalExtension();

        $imageName = md5($filename . time()) . '.' . $extension;

        $entity->setImage($uploadDir . $imageName);
        $entity->getFile()->move($uploadBaseDir . $uploadDir, $imageName);

        $entity->setFile(null);

        return $entity;
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