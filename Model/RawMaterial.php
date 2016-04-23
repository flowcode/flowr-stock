<?php

namespace Flower\StockBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * RawMaterial
 *
 */
abstract class RawMaterial
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"public_api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"public_api"})
     */
    protected $name;

    /**
     * @var float
     *
     * @ORM\Column(name="stock", type="integer")
     * @Groups({"public_api"})
     */
    protected $stock;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\RawMaterialCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     * @Groups({"public_api"})
     */
    protected $category;

    public function __construct()
    {
        $this->stock = 0;
    }


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
     * @return Product
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set category
     *
     * @param \Flower\ModelBundle\Entity\Stock\RawMaterialCategory $category
     * @return Product
     */
    public function setCategory(\Flower\ModelBundle\Entity\Stock\RawMaterialCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Flower\ModelBundle\Entity\Stock\RawMaterialCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return float
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param float $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }


    public function __toString()
    {
        return $this->name;
    }
}
