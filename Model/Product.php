<?php

namespace Flower\StockBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Product
 *
 */
abstract class Product
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
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Groups({"kanban", "full", "public"})
     */
    protected $image;

    /**
     * @var float
     *
     * @ORM\Column(name="sale_price", type="float", nullable=true)
     * @Groups({"public_api"})
     */
    protected $salePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="cost_price", type="float", nullable=true)
     * @Groups({"public_api"})
     */
    protected $costPrice;

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
     * @var boolean
     *
     * @ORM\Column(name="raw_material", type="boolean")
     */
    protected $rawMaterial;

    /**
     * @var boolean
     *
     * @ORM\Column(name="for_sale", type="boolean")
     */
    protected $forSale;

    /**
     * @ORM\ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\ProductCategory")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    protected $category;


    /**
     * @OneToMany(targetEntity="\Flower\ModelBundle\Entity\Stock\ProductRawMaterial", mappedBy="product", cascade={"persist","remove"})
     */
    protected $rawMaterials;

    /**
     * @ORM\ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Clients\Account")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected $supplier;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;


    public function __construct()
    {
        $this->stock = 0;
        $this->enabled = true;
        $this->forSale = false;
        $this->rawMaterial = false;
        $this->rawMaterials = new ArrayCollection();
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
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
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
     * @param \Flower\ModelBundle\Entity\Stock\ProductCategory $category
     * @return Product
     */
    public function setCategory(\Flower\ModelBundle\Entity\Stock\ProductCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Flower\ModelBundle\Entity\Stock\ProductCategory
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

    /**
     * @return mixed
     */
    public function getRawMaterials()
    {
        return $this->rawMaterials;
    }

    /**
     * @return mixed
     */
    public function addRawMaterial(\Flower\ModelBundle\Entity\Stock\ProductRawMaterial $productRawMaterial)
    {
        $this->rawMaterials->add($productRawMaterial);
    }

    /**
     * @return mixed
     */
    public function removeRawMaterial(\Flower\ModelBundle\Entity\Stock\ProductRawMaterial $productRawMaterial)
    {
        if ($this->rawMaterials->contains($productRawMaterial)) {
            $this->rawMaterials->removeElement($productRawMaterial);
        }
    }

    /**
     * @param mixed $rawMaterials
     */
    public function setRawMaterials($rawMaterials)
    {
        $this->rawMaterials = $rawMaterials;
    }

    /**
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param mixed $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return float
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param float $salePrice
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;
    }

    /**
     * @return float
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * @param float $costPrice
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getForSale()
    {
        return $this->forSale;
    }

    /**
     * @param mixed $forSale
     */
    public function setForSale($forSale)
    {
        $this->forSale = $forSale;
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


}
