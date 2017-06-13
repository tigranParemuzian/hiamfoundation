<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingListType extends AbstractType
{
    private $sortOrder = 1;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->sortOrder ++;
//        isset($options['belongs_to']['isEdit'])?$builder->add('sortOrdering', 'hidden', ['data'=>(int)$options['belongs_to']['id'], 'attr'=>['sorting'=>'1']]):$builder->add('sortOrdering', 'hidden', ['attr'=>['sorting'=>'1']]);
        isset($options['belongs_to']['collection'])?$builder->add('collectionValues', null, ['data'=>$options['belongs_to']['collection']]):$builder->add('collectionValues', null);
        $builder->add('sortOrdering', 'hidden', ['data'=>$this->sortOrder,'attr'=>['sorting'=>1]]);
        $builder
            ->add('actualId', 'hidden', ['required'=>false])
            ->add('name', 'text');

            $builder->add('belongsToObjectName', 'hidden', ['data'=>$options['belongs_to']['name']])
            ->add('belongsToObject', 'hidden', ['data'=>$options['belongs_to']['id']])

            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\ListValues',
            'belongs_to'=>[]
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_listing_list_type';
    }


}
