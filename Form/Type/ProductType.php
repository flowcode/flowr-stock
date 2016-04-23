<?php

namespace Flower\StockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Flower\StockBundle\Form\Type\ProductRawMaterialType;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('category', null, array("required" => true))
            ->add('price')
            ->add('enabled', 'checkbox', array(
                'attr' => array('checked' => 'checked'),
                "required" => false
            ))
            ->add('rawMaterials', 'collection', array(
                'type' => new ProductRawMaterialType(),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Stock\Product',
            'translation_domain' => 'Product',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product';
    }
}
