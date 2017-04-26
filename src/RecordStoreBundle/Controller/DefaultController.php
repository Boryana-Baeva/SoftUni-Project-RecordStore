<?php

namespace RecordStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('RecordStoreBundle:Product')->findAll();
        $categories = $em->getRepository('RecordStoreBundle:Category')->findAll();

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'user' => $this->getUser(),
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
