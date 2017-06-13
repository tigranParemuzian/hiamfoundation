<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(isset($options['belongs_to']['label'])){
            $label = 'Have interview';
        }else{
            $label = 'Visible';
        }
        $builder
            ->add('title', 'hidden')
            ->add('value', 'checkbox', ['attr'=>['rows'=>1], 'label'=>$label]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\BooleanValues',
            'belongs_to'=>false
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_boolean_list';
    }
}
