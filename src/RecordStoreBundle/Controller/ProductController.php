<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;

/**
 * Product controller.
 *
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * Lists all product entities.
     *
     * @Route("/products", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('RecordStoreBundle:Product')->findAll();

        return $this->render('product/index.html.twig', array(
            'products' => $products,
            'user' => $this->getUser()
        ));
    }

    /**
     * Creates a new product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('RecordStoreBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setDateCreated(new \DateTime());
            $product->setDateUpdated(new \DateTime());

            $product->setUser($this->getUser());

            /** @var UploadedFile $file */
            $file = $product->getImageForm();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getTitle() . '' . $product->getArtist() . '' . $product->getDateCreated()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/images/product/',
                    $filename
                );

                $product->setImage($filename);

                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();

                $this->addFlash('success', 'Product is created successfully!');

                return $this->redirectToRoute('product_show', array('id' => $product->getId()));
            }
        }
        return $this->render('product/new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
            'user' => $this->getUser()
        ));

    }

    /**
     * Finds and displays a product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);

        if($product->getStock() < 1){
            $this->addFlash('error', 'Product is out of stock!');
            return $this->redirectToRoute('product_index', array('id' => $product->getId()));
        }

        return $this->render('product/show.html.twig', array(
            'product' => $product,
            'delete_form' => $deleteForm->createView(),
            'user' => $this->getUser()
        ));
    }

    /**
     * Displays a form to edit an existing product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product)
    {
        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('RecordStoreBundle\Form\ProductType', $product);
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
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product)
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
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
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
