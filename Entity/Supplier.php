<?php

namespace Flower\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * Supplier
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Flower\StockBundle\Entity\SupplierRepository")
 */
class Supplier
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
     * @var string
     *
     * @ORM\Column(name="businessName", type="string", length=255)
     */
    private $businessName;

    /**
     * @ManyToOne(targetEntity="\Flower\FinancesBundle\Entity\Account")
     * @JoinColumn(name="finance_account_id", referencedColumnName="id")
     */
    protected $financeAccount;


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
     * @return Supplier
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
     * Set businessName
     *
     * @param string $businessName
     * @return Supplier
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * @return mixed
     */
    public function getFinanceAccount()
    {
        return $this->financeAccount;
    }

    /**
     * @param mixed $financeAccount
     */
    public function setFinanceAccount($financeAccount)
    {
        $this->financeAccount = $financeAccount;
    }

    function __toString()
    {
        return $this->getName();
    }


}
