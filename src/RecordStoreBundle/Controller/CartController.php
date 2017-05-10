<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\CartOrder;
use RecordStoreBundle\Entity\Product;
use RecordStoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;

class CartController extends Controller
{
    const STATUS_ADDED = 'ADDED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_ORDERED = 'ORDERED';

    /**
     * @Route("/cart", name="cart_index")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $user = $this->getUser();
        $addedOrders = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_ADDED, $user);

        $pendingOrders = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_PENDING, $user);

        $orders = array_merge($addedOrders, $pendingOrders);

        $calculator = $this->get('price_calculator');

        $totalPrice = 0;
        $promotionalTotalPrice = 0;
        foreach ($orders as $order) {
            /**
             * @var CartOrder $order
             */
            $totalPrice += $order->getProduct()->getPrice() * $order->getQuantity();

            $promotionalTotalPrice += $order->getTotalPrice();
        }

        return $this->render('cart/cart.html.twig', array(
            'user' => $user,
            'orders' => $orders,
            'calculator' => $calculator,
            'total_price' => $totalPrice,
            'promotional_total_price' => $promotionalTotalPrice,
        ));
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     * @Method("POST")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Product $product, Request $request)
    {
        $order = new CartOrder();
        $order->setProduct($product);
        $order->setUser($this->getUser());
        $order->setCreatedOn(new \DateTime());
        $order->setStatus(self::STATUS_ADDED);

        $calculator = $this->get('price_calculator');
        $singlePrice = $calculator->calculate($product);

        $order->setSinglePrice($singlePrice);

        if ($request->request->get('quantity') !== null) {
            $order->setQuantity($request->request->get('quantity'));
        } else {
            $order->setQuantity(1);
        }

        $totalPrice = $order->getSinglePrice() * $order->getQuantity();
        $order->setTotalPrice($totalPrice);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $this->addFlash('success', 'Product was added to cart successfully!');

        return $this->redirectToRoute('cart_index', array(
            'user' => $this->getUser(),
        ));
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     */
    public function checkoutAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $addedOrders = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_ADDED, $user);

        $pendingOrders = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_PENDING, $user);

        $orders = array_merge($addedOrders, $pendingOrders);

        $totalCartPrice = 0;
        foreach ($orders as $order) {
            /**
             * @var CartOrder $order
             */
            $totalCartPrice += $order->getTotalPrice();

            if ($order->getQuantity() > $order->getProduct()->getStock()->getQuantity()) {

                $this->addFlash('error', 'There is not enough of ' . $order->getProduct()->getArtist() .
                    ' - ' . $order->getProduct()->getTitle() . ' in stock.');

                return $this->redirectToRoute('cart_index', array(
                    'user' => $user
                ));
            }
        }
        if ($totalCartPrice > $user->getCash()) {

            $this->addFlash('error', 'Insufficient amount of cash in your account.');

            return $this->redirectToRoute('cart_index', array(
                'user' => $user
            ));

        } else {
            foreach ($orders as $order) {

                if($order->getStatus() === self::STATUS_ADDED){
                    $order->setStatus(self::STATUS_PENDING);
                }
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }

            return $this->render('cart/checkout.html.twig', array(
                'user' => $user,
                'orders' => $orders,
                'total_cart_price' => $totalCartPrice
            ));
        }
    }

    /**
     * @Route("/cart/checkout/payment", name="cart_payment")
     */
    public function paymentAction()
    {
        $user = $this->getUser();
        $pendingOrders = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_PENDING, $user);

        foreach ($pendingOrders as $order) {
            /**
             * @var CartOrder $order
             */
            $order->setStatus(self::STATUS_ORDERED);

            $remainingStock = $order->getProduct()->getStock()->getQuantity() - $order->getQuantity();
            $order->getProduct()->getStock()->setQuantity($remainingStock);

            $remainingCash = $order->getUser()->getCash() - $order->getTotalPrice();
            $order->getUser()->setCash($remainingCash);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        $this->addFlash('success', 'Payment was successful.');

        return $this->redirectToRoute('cart_index', array(
            'user' => $user
        ));
    }

    /**
     * @Route("/user/products", name="my_products")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showUsersProductsAction()
    {
        $user = $this->getUser();
        $calculator = $this->get('price_calculator');

        $myProducts = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:CartOrder')
            ->fetchUsersOrdersByStatus(self::STATUS_ORDERED, $user);

        return $this->render('user/my_products.html.twig', array(
            'user' => $user,
            'calculator' => $calculator,
            'my_products' => $myProducts
        ));
    }

    /**
     * Deletes a product entity.
     *
     * @Route("cart/delete/{id}", name="order_delete")
     * @Method("GET")
     * @param CartOrder $order
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(CartOrder $order)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        $this->addFlash('success', 'Order was deleted successfully!');

        return $this->redirectToRoute('cart_index', array(
            'user' => $this->getUser(),
        ));
    }
}