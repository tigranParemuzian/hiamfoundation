<?php

namespace AppBundle\Form;

use AppBundle\Entity\AttributesDefinition;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextListType extends AbstractType
{
    private $texts;
    private $interation;

    public function __construct($textes)
    {
        $this->texts = $textes;
        $this->interation  = 0;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ckeditor = false;
        $isList = false;
        if(!is_null($this->texts) ){

                $current = $this->texts[(int)$builder->getName()];
                if($current->getFormType() == 'ckeditor' ){
                    $ckeditor = true;
                }

                if( $current->getFormType()=='choice-category'){
                    $isList = true;
                    $categoryes = ['Technology'=>'Technology', 'Tourism'=>'Tourism', 'Community'=>'Local communities & products'];
                }

                if( $current->getFormType()=='choice-members'){
                    $isList = true;
                    $categoryes = ['Staff'=>'Staff', 'Ambassador'=>'Ambassador', 'Board Member'=>'Board Member'];
                }

        }

        $builder
            ->add('title', 'hidden');

        if($ckeditor == true || $isList == true){
            if($ckeditor === true){
                $builder
                    ->add('value', 'ckeditor', array(
                        'label' => ' ',
                        'required' => false,
                        'trim' => true,
                        'auto_inline'=>true,
                        'config' => array(
                            'uiColor' => '#ffffff',
                            'required'=>true)
                    )) ;
            }else{
                $builder
                    ->add('value', 'choice', ['choices'=>
                        $categoryes,
                        'label_attr'=>['class'=>'hidden'], 'label'=>''])
                ;
            }
        }else{
            $builder->add('value', 'text', array('label_attr'=>['class'=>'hidden'], 'label'=>''));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\TextValues',
            'belongs_to'=>false
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_text_list';
    }
}
