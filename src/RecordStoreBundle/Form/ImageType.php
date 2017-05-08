<?php

namespace RecordStoreBundle\Form;

use RecordStoreBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("url", FileType::class, [
            "label" => false,
            'data_class' => null,
            'attr' => [
                'style' => 'display:none'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Image::class]);
    }

    public function getName()
    {
        return 'record_store_bundle_image_type';
    }
}
