<?php

namespace RecordStoreBundle\Form;

use Doctrine\DBAL\Types\DecimalType;
use Doctrine\ORM\EntityManager;
use RecordStoreBundle\Entity\Category;
use RecordStoreBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', TextType::class)
            ->add('artist', TextType::class)
            ->add('yearOfRelease', ChoiceType::class, [
                'choices' => array_combine(range(date('Y'), date('Y') - 100), range(date('Y'), date('Y') - 100))
            ])
            ->add('category', EntityType::class, [
                'class' => 'RecordStoreBundle:Category',
                'choice_label' => 'name',
                'label' => 'Genre'
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('image_form', FileType::class, [
                'data_class' => null,
                'required' => false,
                'label' => 'Album Cover'
            ])
            ->add('price', MoneyType::class)
            ->add('stock', IntegerType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RecordStoreBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'recordstorebundle_product';
    }


}
