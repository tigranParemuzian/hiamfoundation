<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'hidden', array('required' => false, 'label' => false))
            ->add('file', 'icon_type', array('required' => true, 'label' => false, 'label_attr'=>['class'=>'hidden']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Image',
            'belongs_to'=>false
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_file_list_type';
    }
}
