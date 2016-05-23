<?php

namespace Flower\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * StockLevel
 *
 * @ORM\Table(name="stock_level")
 * @ORM\Entity(repositoryClass="Flower\StockBundle\Repository\StockLevelRepository")
 */
class StockLevel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\Product")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;

    /**
     * @ManyToOne(targetEntity="\Flower\CoreBundle\Entity\NotificationChannelImpl")
     * @JoinColumn(name="notification_channel_id", referencedColumnName="id")
     */
    protected $notificationChannel;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return StockLevel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return StockLevel
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set product
     *
     * @param \Flower\ModelBundle\Entity\Stock\Product $product
     * @return StockLevel
     */
    public function setProduct(\Flower\ModelBundle\Entity\Stock\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Flower\ModelBundle\Entity\Stock\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set notificationChannel
     *
     * @param \Flower\CoreBundle\Entity\NotificationChannel $notificationChannel
     * @return StockLevel
     */
    public function setNotificationChannel(\Flower\CoreBundle\Entity\NotificationChannel $notificationChannel = null)
    {
        $this->notificationChannel = $notificationChannel;

        return $this;
    }

    /**
     * Get notificationChannel
     *
     * @return \Flower\CoreBundle\Entity\NotificationChannel
     */
    public function getNotificationChannel()
    {
        return $this->notificationChannel;
    }
}
