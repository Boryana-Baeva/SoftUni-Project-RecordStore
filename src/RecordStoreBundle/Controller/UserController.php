<?php

namespace RecordStoreBundle\Controller;

use ClassesWithParents\D;
use RecordStoreBundle\Entity\Image;
use RecordStoreBundle\Entity\User;
use RecordStoreBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * Finds and displays user profile.
     *
     * @Route("user/{id}", name="user_profile")
     * @Method("GET")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction(User $user)
    {
        $deleteAccountForm = $this->createDeleteForm($user);
        $avatarForm = $this->createForm('RecordStoreBundle\Form\ImageType', $user->getAvatar());

        return $this->render('user/profile.html.twig', array(
            'user' => $user,
            'delete_account_form' => $deleteAccountForm->createView(),
            'avatar_form' => $avatarForm->createView()
        ));
    }

    /**
     * Displays a form to upload user's profile picture.
     *
     * @Route("user/{id}", name="avatar_upload")
     * @Method({"POST"})
     */
    public function uploadProfilePictureAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        /*if (null !== $user->getAvatar()->getUrl()) {
            unlink($this->get('kernel')->getRootDir() . '/../web' . $user->getAvatar()->getUrl());
            $user->getAvatar()->setUser(null);
            $em->remove($user->getAvatar());
            $em->flush();
        }*/

        $avatar = new Image();
        $avatarForm = $this->createForm(ImageType::class, $avatar);
        $deleteAccountForm = $this->createDeleteForm($user);

        $avatarForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {

            $avatar = $this->get("app.image_uploader")->uploadAvatar($avatar);
            $avatar->setUser($user);
            $user->setAvatar($avatar);
            $em->persist($avatar);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profile picture has been uploaded successfully!');

            return $this->redirectToRoute('user_profile', array('id' => $user->getId()));
        }

        return $this->render('user/profile.html.twig', array(
            'avatar_form' => $avatarForm->createView(),
            'delete_account_form' => $deleteAccountForm->createView(),
            'user' => $this->getUser()
        ));
    }


    /**
     * Displays a form to edit user profile.
     *
     * @Route("user/{id}/edit", name="profile_edit")
     * @Method({"GET", "POST"})
     */
    public function editProfileAction(Request $request, User $user)
    {

        $editProfileForm = $this->createForm('RecordStoreBundle\Form\UserProfileType', $user);
        $editProfileForm->handleRequest($request);

        if ($editProfileForm->isSubmitted() && $editProfileForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Profile has been edited successfully!');

            return $this->redirectToRoute('user_profile', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'edit_profile_form' => $editProfileForm->createView(),
            'user' => $user
        ));
    }

    /**
     * Displays a form to change user's password.
     *
     * @Route("user/{id}/change", name="change_password")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction(Request $request, User $user)
    {

        $changePasswordForm = $this->createForm('RecordStoreBundle\Form\UserChangePasswordType', $user);
        $changePasswordForm->handleRequest($request);

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {

            $newPassword = $changePasswordForm->get('rawPassword')->getData();
            $encoder = $this->get('security.password_encoder');

            $user->setPassword(
                $encoder->encodePassword($user, $newPassword)
            );

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Password has been changed successfully!');

            return $this->redirectToRoute('user_profile', array('id' => $user->getId()));
        }

        return $this->render('user/change_password.html.twig', array(
            'change_password_form' => $changePasswordForm->createView(),
            'user' => $user
        ));
    }

    /**
     * Deletes  user entity.
     *
     * @Route("user/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAccountAction(Request $request, User $user)
    {
        $delete_account_form = $this->createDeleteForm($user);
        $delete_account_form->handleRequest($request);

        if ($delete_account_form->isSubmitted() && $delete_account_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->getAvatar()->setUser(null);
            $em->flush();
            $em->remove($user->getAvatar());
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Your account has been successfully deleted. We are sorry to see you go!');

        }
        $this->get('security.token_storage')->setToken(null);
        $this->get('session')->clear();
        return $this->redirectToRoute('homepage');
    }

    /**
     * Creates a form to delete a user account.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


}
