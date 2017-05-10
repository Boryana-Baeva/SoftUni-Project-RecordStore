<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\Product;
use RecordStoreBundle\Entity\Stock;
use RecordStoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;

class ProductController extends Controller
{
    const PAGE_LIMIT = 12;

    /**
     * Lists all product entities.
     *
     * @Route("/admin/product/list", name="product_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('RecordStoreBundle:Product')->findAll();

        $paginator = $this->get('knp_paginator');
        $query = $this->getDoctrine()->getRepository('RecordStoreBundle:Product')->createQueryBuilder('p')
            ->select('p');

        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            self::PAGE_LIMIT
        );

        return $this->render('product/index.html.twig', array(
            'pagination' => $pagination,
            'products' => $products,
            'user' => $this->getUser()
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/admin/product/new", name="product_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $stock = new Stock();
        $formCreate = $this->createForm('RecordStoreBundle\Form\StockProductType', $stock);
        $formCreate->handleRequest($request);

        if ($formCreate->isSubmitted() && $formCreate->isValid()) {

            $product = $stock->getProduct();

            $product->setDateCreated(new \DateTime());
            $product->setDateUpdated(new \DateTime());

            $product->setUser($this->getUser());

            /** @var UploadedFile $file */
            $file = $product->getImageForm();

            if (!$file) {
                $formCreate->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getTitle() . '' . $product->getArtist() . '' . $product->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->persist($stock);

                $em->flush();

                $this->addFlash('success', 'Product is created successfully!');

                return $this->redirectToRoute('product_show', array('id' => $product->getId()));
            }
        }
        return $this->render('product/new.html.twig', [
            'user' => $this->getUser(),
            'formCreate' => $formCreate->createView(),
        ]);

    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("product/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $deleteForm = $this->createDeleteForm($product);

        if ($product->getStock()->getQuantity() < 1) {

            if (!$this->getUser() || $this->getUser()->getRole() == 'ROLE_USER') {
                $this->addFlash('error', 'Product is out of stock!');
                return $this->redirectToRoute('homepage', array('id' => $product->getId()));
            }
        }


        $calculator = $this->get('price_calculator');

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
            'user' => $this->getUser(),
            'calculator' => $calculator
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("admin/product/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {

        $deleteForm = $this->createDeleteForm($product);
        $stock = $product->getStock();
        $editForm = $this->createForm('RecordStoreBundle\Form\StockProductType', $stock);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $product->setDateUpdated(new \DateTime());

            if ($product->getImageForm() instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $product->getImageForm();

                $filename = md5($product->getTitle() . '' . $product->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Product is edited successfully!');

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('product/edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'user' => $this->getUser()
        ));
    }


    /**
     * Deletes a product entity.
     *
     * @Route("admin/product/delete/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $stock = $product->getStock();
        $orders = $product->getOrders();
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stock);
            foreach ($orders as $order){
                $em->remove($order);
            }
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Product is deleted successfully!');
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a product entity.
     *
     * @param Product $product The product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/category/{category}", name="products_categorized")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($category, Request $request)
    {
        $cat = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:Category')
            ->find($category);

        $categories = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:Category')
            ->findAll();
        sort($categories);

        $artists = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:Product')
            ->fetchArtists();

        $paginator = $this->get('knp_paginator');
        $query = $this->getDoctrine()->getRepository('RecordStoreBundle:Product')->findByCategory($cat);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGE_LIMIT
        );

        $calculator = $this->get('price_calculator');

        return $this->render('product/list.html.twig', array(
            'category' => $cat,
            'categories' => $categories,
            'artists' => $artists,
            'pagination' => $pagination,
            'user' => $this->getUser(),
            'calculator' => $calculator
        ));
    }

    /**
     * @Route("/artist/{artist}", name="products_by_artists")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function artistAction($artist, Request $request)
    {
        $categories = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:Category')
            ->findAll();

        $artists = $this->getDoctrine()
            ->getRepository('RecordStoreBundle:Product')
            ->fetchArtists();

        $paginator = $this->get('knp_paginator');
        $query = $this->getDoctrine()->getRepository('RecordStoreBundle:Product')->findByArtist($artist);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGE_LIMIT
        );

        $calculator = $this->get('price_calculator');

        return $this->render('product/list-by-artist.html.twig', array(
            'artist' => $artist,
            'artists' => $artists,
            'categories' => $categories,
            'pagination' => $pagination,
            'user' => $this->getUser(),
            'calculator' => $calculator
        ));
    }
}
