<?php

namespace RecordStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class)
                ->add('email', EmailType::class)
                ->add('rawPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options' => [
                            'label' => 'Password'
                        ],
                        'second_options' => [
                            'label' => 'Confirm Password'
                        ]
                ])
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('phoneNumber', TextType::class)
                ->add('Register', SubmitType::class, ['attr' =>['class' => 'btn btn-primary']]);
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
