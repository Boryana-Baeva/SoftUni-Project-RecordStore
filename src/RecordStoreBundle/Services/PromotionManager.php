<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 5.5.2017 г.
 * Time: 16:19 ч.
 */

namespace RecordStoreBundle\Services;


use RecordStoreBundle\Entity\Category;
use RecordStoreBundle\Repository\PromotionRepository;

class PromotionManager
{
    private $general_promotion;

    private $category_promotions;

    /**
     * @param PromotionRepository $repo
     */
    public function __construct(PromotionRepository $repo)
    {
        $this->general_promotion =  $repo->fetchBiggestGeneralPromotion();
        $this->category_promotions = $repo->fetchCategoriesPromotions();
    }


    /**
     * @return int
     */
    public function getGeneralPromotion()
    {
        return $this->general_promotion ?? 0;
    }

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function hasCategoryPromotion($category)
    {
        return array_key_exists($category->getId(), $this->category_promotions);
    }

    /**
     * @param Category $category
     *
     * @return int
     */
    public function getCategoryPromotion($category)
    {
        return $this->category_promotions[$category->getId()];
    }
}