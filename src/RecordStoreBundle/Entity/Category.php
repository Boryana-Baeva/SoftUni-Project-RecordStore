<?php

namespace RecordStoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="RecordStoreBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ORM\OneToMany(targetEntity="RecordStoreBundle\Entity\Product", mappedBy="category")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="RecordStoreBundle\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="RecordStoreBundle\Entity\Promotion", mappedBy="category")
     */
    private $promotions;
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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @param Product $product
     */
    public function addProduct($product){
        $this->getProducts()->add($product);
    }

    /**
     * @return Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param Collection $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }
    
    
}

