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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
    const INITIAL_CASH = 100;

    /**
     * @Route("/login", name="user_login" )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute("homepage");
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        if($error){
            $this->addFlash(
                'error',
                'Login unsuccessful! Please try again.'
            );
        } /*else{
            $this->addFlash(
                'success',
                'Welcome! You have successfully logged in.'
            );
        }*/
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

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->redirectToRoute("homepage");
        }

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

        if($form->isSubmitted() && $form->isValid()){

            $user->setRoles([ $user->getDefaultRole() ]);

            $encoder = $this->get('security.password_encoder');

            $user->setPassword(
                $encoder->encodePassword($user, $user->getRawPassword())
            );

            $user->setCash(self::INITIAL_CASH);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->addFlash(
                'success',
                'Congratulations! You registered successfully!'
            );
            return $this->redirectToRoute("homepage");
        }

        return $this->render("security/register.html.twig", ['form' => $form->createView()] );
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logoutAction(){

    }
}
