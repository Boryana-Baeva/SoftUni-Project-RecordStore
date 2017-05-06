<?php

namespace RecordStoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartOrder
 *
 * @ORM\Table(name="cart_orders")
 * @ORM\Entity(repositoryClass="RecordStoreBundle\Repository\CartOrderRepository")
 */
class CartOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="RecordStoreBundle\Entity\User",inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="RecordStoreBundle\Entity\Product",inversedBy="orders")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="singlePrice", type="decimal", precision=10, scale=2)
     */
    private $singlePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="totalPrice", type="decimal", precision=10, scale=2)
     */
    private $totalPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdOn", type="date")
     */
    private $createdOn;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return CartOrder
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set product
     *
     * @param string $product
     *
     * @return CartOrder
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartOrder
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set singlePrice
     *
     * @param string $singlePrice
     *
     * @return CartOrder
     */
    public function setSinglePrice($singlePrice)
    {
        $this->singlePrice = $singlePrice;

        return $this;
    }

    /**
     * Get singlePrice
     *
     * @return string
     */
    public function getSinglePrice()
    {
        return $this->singlePrice;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     *
     * @return CartOrder
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return CartOrder
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }
}

