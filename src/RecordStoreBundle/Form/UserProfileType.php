<?php

namespace RecordStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, [
            'attr' => [

                'placeholder' => "Username",
            ]
        ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => "First Name",
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => "Last Name"
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'placeholder' => "Phone Number"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RecordStoreBundle\Entity\User',
        ]);
    }

    public function getName()
    {
        return 'record_store_bundle_user_profile_type';
    }
}
