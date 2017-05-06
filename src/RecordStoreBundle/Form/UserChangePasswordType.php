<?php

namespace RecordStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPlainPassword', \Symfony\Component\Form\Extension\Core\Type\PasswordType::class, array(
            'constraints' => array(
                new \Symfony\Component\Security\Core\Validator\Constraints\UserPassword(),
            ),
            'mapped' => false,
            'required' => true,
            'attr' => [
                'placeholder' => 'Enter current password'
            ],
            'label' => 'Current Password',
        ))
            ->add('rawPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Password'
                ],
                'first_options' => [
                    'label' => 'New Password',
                    'attr' => ['placeholder' => 'Enter new password']
                ],
                'second_options' => [
                    'label' => 'Confirm New Password',
                    'attr' => ['placeholder' => 'Confirm new password']
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RecordStoreBundle\Entity\User'
        ]);
    }

    public function getName()
    {
        return 'record_store_bundle_user_change_password_type';
    }
}
