<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\User;
use RecordStoreBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="user_login" )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("security/login.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);

    }

    /**
     * @Route("/register", name="user_register")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(){

        $form = $this->createForm(UserType::class);
        return $this->render("security/register.html.twig", ['form' => $form->createView()] );
    }

    /**
     * @Route("/register", name="user_register_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()){

            $encoder = $this->get('security.password_encoder');
            $user->setPassword(
                $encoder->encodePassword($user, $user->getRawPassword())
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render("security/register.html.twig", ['form' => $form->createView()] );
    }
}
