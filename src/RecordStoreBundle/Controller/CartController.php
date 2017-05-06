<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\CartOrder;
use RecordStoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;

class CartController extends Controller
{
    /**
     * @Route("cart/{id}", name="cart_index")
     * @Method("GET")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(User $user)
    {
        /**
         * @var CartOrder|Collection
         */
        $orders = $user->getOrders();

        $calculator = $this->get('price_calculator');

        return $this->render('cart/cart.html.twig', array(
            'user' => $user,
            'orders' => $orders,
            'calculator' => $calculator
        ));
    }
}
