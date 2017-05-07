<?php

namespace RecordStoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="RecordStoreBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="Please enter title of the album")
     * @Assert\Length(max="255")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", type="string", length=255)
     * @Assert\NotBlank(message="Please enter name of the artist")
     * @Assert\Length(max="255")
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="yearOfRelease", type="string", length=255)
     * @Assert\NotBlank(message="Please enter year of release")
     */
    private $yearOfRelease;

    /**
     * @ORM\ManyToOne(targetEntity="RecordStoreBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Please select a category")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     *
     */
    private $image;

    /**
     * @Assert\Image(mimeTypes={"image/png", "image/jpeg"},
     *               maxSize="5M", maxSizeMessage="Maximum file size is 5MB")
     */
    private $image_form;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var Stock
     *
     * @ORM\OneToOne(targetEntity="RecordStoreBundle\Entity\Stock", mappedBy="product")
     */
    private $stock;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdated", type="datetime")
     */
    private $dateUpdated;


    /**
     * @ORM\ManyToOne(targetEntity="RecordStoreBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="RecordStoreBundle\Entity\CartOrder", mappedBy="product")
     * @ORM\OneToMany(targetEntity="RecordStoreBundle\Entity\CartOrder", mappedBy="user")
     *
     */
    private $orders;

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
     * @param string $title
     *
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param string $artist
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return string
     */
    public function getYearOfRelease()
    {
        return $this->yearOfRelease;
    }

    /**
     * @param string $yearOfRelease
     */
    public function setYearOfRelease($yearOfRelease)
    {
        $this->yearOfRelease = $yearOfRelease;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
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
     * Set image
     *
     * @param string $image
     *
     * @return Product
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set price
     *
     * @param string $price
     *
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
     * Set stock
     *
     * @param Stock $stock
     *
     * @return Product
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }


    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Product
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     *
     * @return Product
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @return mixed
     */
    public function getImageForm()
    {
        return $this->image_form;
    }

    /**
     * @param mixed $image_form
     */
    public function setImageForm($image_form)
    {
        $this->image_form = $image_form;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param CartOrder $order
     */
    public function addOrder($order){
        $this->getOrders()->add($order);
    }
}

