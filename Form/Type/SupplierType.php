<?php

namespace Flower\StockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupplierType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('businessName')
            ->add('financeAccount')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\StockBundle\Entity\Supplier',
            'translation_domain' => 'Supplier',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'supplier';
    }
}
