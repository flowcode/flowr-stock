<?php

namespace Flower\StockBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * RawMaterial
 *
 */
abstract class RawMaterialCategory
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
     * @ORM\OneToMany(targetEntity="\Flower\ModelBundle\Entity\Stock\RawMaterial", mappedBy="category")
     */
    protected $rawMaterials;

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
     * @return ProductCategory
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
     * Constructor
     */
    public function __construct()
    {
        $this->rawMaterials = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getRawMaterials()
    {
        return $this->rawMaterials;
    }

    /**
     * @param mixed $rawMaterials
     */
    public function setRawMaterials($rawMaterials)
    {
        $this->rawMaterials = $rawMaterials;
    }

    
    public function __toString()
    {
        return $this->name;
    }
}
