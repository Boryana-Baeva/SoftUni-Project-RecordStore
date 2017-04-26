<?php

namespace RecordStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'attr' => [

                'placeholder' => "Username",

            ],
            'label' => false

        ])
            ->add('email', EmailType::class, [
                'attr' => [

                    'placeholder' => "Email Address"
                ],
                'label' => false
            ])
            ->add('rawPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'attr' => [
                    'placeholder' => 'Password'
                ],
                'first_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Enter password']
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Confirm password']
                ]
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => "First Name"
                ],
                'label' => false
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => "Last Name"
                ],
                'label' => false
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'placeholder' => "Phone Number"
                ],
                'label' => false
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
        return 'record_store_bundle_user_type';
    }
}
