<?php

namespace RecordStoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    const PAGE_LIMIT = 9;

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('RecordStoreBundle:Category')->findAll();
        $artists = $em->getRepository('RecordStoreBundle:Product')->fetchArtists();

        $paginator = $this->get('knp_paginator');
        $query = $this->getDoctrine()->getRepository('RecordStoreBundle:Product')->fetchAvailable();

        $pagination =  $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGE_LIMIT
        );

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'user' => $this->getUser(),
            'pagination' => $pagination,
            'categories' => $categories,
            'artists' => $artists
        ]);
    }
}
