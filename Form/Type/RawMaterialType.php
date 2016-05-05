<?php

namespace Flower\StockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RawMaterialType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('stock')
            ->add('enabled')
            ->add('supplier')
            ->add('category');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Stock\RawMaterial',
            'translation_domain' => 'RawMaterial',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rawmaterial';
    }
}
