<?php

namespace Flower\StockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductRawMaterialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rawMaterial')
            ->add('measureUnit')
            ->add('quantity')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Stock\ProductRawMaterial',
            'translation_domain' => 'ProductRawMaterial',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'productrawmaterial';
    }
}
