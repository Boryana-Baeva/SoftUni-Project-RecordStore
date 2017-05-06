<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5.5.2017 г.
 * Time: 16:21 ч.
 */

namespace RecordStoreBundle\Services;


use RecordStoreBundle\Entity\Category;
use RecordStoreBundle\Entity\Product;
use RecordStoreBundle\Entity\Promotion;

class PriceCalculator
{
    /** @var  PromotionManager */
    private $manager;

    /**
     * @var integer
     */
    private $relevantPromotion;

    public function __construct(PromotionManager $manager) {
        $this->manager= $manager;
    }

    /**
     * @return integer
     */
    public function getRelevantPromotion()
    {
        return $this->relevantPromotion;
    }

    /**
     * @param integer $relevantPromotion
     */
    public function setRelevantPromotion($relevantPromotion)
    {
        $this->relevantPromotion = $relevantPromotion;
    }



    /**
     * @param Product $product
     *
     * @return float
     */
    public function calculate($product)
    {
        /**
         * @var Category $category
         */
        $category    = $product->getCategory();
        $category_id = $category->getId();

        $promotion = $this->manager->getGeneralPromotion();

        if($this->manager->hasCategoryPromotion($category)
            && $this->manager->getCategoryPromotion($category) > $promotion){

            $promotion = $this->manager->getCategoryPromotion($category);
        }

        $this->setRelevantPromotion($promotion);

        return $product->getPrice() - $product->getPrice() * ($promotion / 100);
    }
}