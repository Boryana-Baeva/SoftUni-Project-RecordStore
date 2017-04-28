<?php

namespace RecordStoreBundle\Controller;

use RecordStoreBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * Finds and displays user profile.
     *
     * @Route("user/{id}", name="user_profile")
     * @Method("GET")
     */
    public function profileAction(User $user)
    {
        
        return $this->render('user/profile.html.twig', array('user' => $user));
    }
}
