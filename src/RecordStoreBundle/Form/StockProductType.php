<?php

namespace RecordStoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class StockProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', ProductType::class, array(
                "constraints" => new Valid(),
                'label' => false
                ))
            ->add('quantity', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RecordStoreBundle\Entity\Stock'
        ));
    }

    public function getName()
    {
        return 'record_store_bundle_stock_type';
    }
}