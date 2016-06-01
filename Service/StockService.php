<?php

namespace Flower\StockBundle\Service;

use DoctrineExtensions\Query\Mysql\Date;
use Flower\ModelBundle\Entity\Sales\Sale;
use Flower\ModelBundle\Entity\Stock\Product;
use Flower\StockBundle\Entity\StockChangeLog;
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
    public function increaseProduct(Product $product, $quantity = 1, $affectRawMaterialStock = true, $comments = null, \DateTime $date = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');


        /* product stock */
        $previousStock = $product->getStock();
        $product->setStock($previousStock + $quantity);

        if (!$date) {
            $date = new \DateTime();
        }

        /* stock change log */
        $stockChangeLog = new StockChangeLog();
        $stockChangeLog->setProduct($product);
        $stockChangeLog->setAmount($quantity);
        $stockChangeLog->setBalance($previousStock + $quantity);
        $stockChangeLog->setType(StockChangeLog::TYPE_ENTRY);
        $stockChangeLog->setDate($date);
        $stockChangeLog->setDescription($comments);

        $em->persist($stockChangeLog);
        $em->flush();

        if ($affectRawMaterialStock) {
            foreach ($product->getRawMaterials() as $productRawMaterial) {
                $rawMaterial = $productRawMaterial->getRawMaterial();

                $this->decreaseProduct($rawMaterial, $quantity * $productRawMaterial->getQuantity(), null);
            }
        }
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    public function decreaseProduct(Product $product, $quantity = 1, Sale $sale = null, $comments = null, \DateTime $date = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* product stock */
        $previousStock = $product->getStock();
        $product->setStock($previousStock - $quantity);

        if (!$date) {
            $date = new \DateTime();
        }

        /* stock change log */
        $stockChangeLog = new StockChangeLog();
        $stockChangeLog->setProduct($product);
        $stockChangeLog->setAmount($quantity);
        $stockChangeLog->setBalance($previousStock - $quantity);
        $stockChangeLog->setType(StockChangeLog::TYPE_EXIT);
        $stockChangeLog->setDate($date);
        $stockChangeLog->setSale($sale);
        $stockChangeLog->setDescription($comments);

        $em->persist($stockChangeLog);
        $em->flush();

        /* check notification level */
        $this->checkLevel($product);
    }

    public function checkLevel(Product $product)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $stockLevels = $em->getRepository('FlowerStockBundle:StockLevel')->findBy(array(
            'product' => $product,
        ));

        foreach ($stockLevels as $stockLevel) {
            if ($product->getStock() <= $stockLevel->getStock()) {

                $notificationChannel = $stockLevel->getNotificationChannel();

                $handlerId = "flower.core.service.notification_handler." . $notificationChannel->getType();
                $notificationChannelHandler = $this->container->get($handlerId);

                $notificationChannelHandler->handle($notificationChannel, array("product" => $product, "level" => $stockLevel->getStock()));
            }
        }
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