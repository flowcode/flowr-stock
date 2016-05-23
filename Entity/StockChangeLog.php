<?php

namespace Flower\StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
/**
 * StockChangeLog
 *
 * @ORM\Table(name="stock_stock_change_log")
 * @ORM\Entity(repositoryClass="Flower\StockBundle\Repository\StockChangeLogRepository")
 */
class StockChangeLog
{

    const TYPE_ENTRY = 0;
    const TYPE_EXIT = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Stock\Product")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\Sales\Sale")
     * @JoinColumn(name="sale_id", referencedColumnName="id")
     */
    protected $sale;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @ManyToOne(targetEntity="\Flower\ModelBundle\Entity\User\User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;


    public function __construct()
    {
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
     * Set date
     *
     * @param \DateTime $date
     * @return StockChangeLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return StockChangeLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return StockChangeLog
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set balance
     *
     * @param integer $balance
     * @return StockChangeLog
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return StockChangeLog
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set product
     *
     * @param \Flower\ModelBundle\Entity\Stock\Product $product
     * @return StockChangeLog
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
     * Set user
     *
     * @param \Flower\ModelBundle\Entity\User\User $user
     * @return StockChangeLog
     */
    public function setUser(\Flower\ModelBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Flower\ModelBundle\Entity\User\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sale
     *
     * @param \Flower\ModelBundle\Entity\Sales\Sale $sale
     * @return StockChangeLog
     */
    public function setSale(\Flower\ModelBundle\Entity\Sales\Sale $sale = null)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return \Flower\ModelBundle\Entity\Sales\Sale 
     */
    public function getSale()
    {
        return $this->sale;
    }
}
