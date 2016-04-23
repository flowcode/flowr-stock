<?php

namespace Flower\StockBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * ProductRawMaterial
 *
 */
abstract class ProductRawMaterial
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
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\Product", inversedBy="rawMaterials")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\RawMaterial")
     * @JoinColumn(name="raw_material_id", referencedColumnName="id")
     */
    protected $rawMaterial;

    /**
     * @ManyToOne(targetEntity="\Flower\StockBundle\Entity\MeasureUnit")
     * @JoinColumn(name="measure_unit_id", referencedColumnName="id")
     */
    protected $measureUnit;

    /**
     * @var float
     *
     * @ORM\Column(name="stock", type="integer")
     * @Groups({"public_api"})
     */
    protected $quantity;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getRawMaterial()
    {
        return $this->rawMaterial;
    }

    /**
     * @param mixed $rawMaterial
     */
    public function setRawMaterial($rawMaterial)
    {
        $this->rawMaterial = $rawMaterial;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getMeasureUnit()
    {
        return $this->measureUnit;
    }

    /**
     * @param mixed $measureUnit
     */
    public function setMeasureUnit($measureUnit)
    {
        $this->measureUnit = $measureUnit;
    }

    


}
