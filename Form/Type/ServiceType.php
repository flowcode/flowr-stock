<?php

namespace Flower\StockBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('price')
            ->add('enabled', 'checkbox', array(
                'attr'     => array('checked'   => 'checked'),
                "required" => false
            ));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Stock\Service',
            'translation_domain' => 'Service',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'service';
    }
}
