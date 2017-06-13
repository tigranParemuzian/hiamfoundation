<?php

namespace AppBundle\Form;

use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\ListValues;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageDocument extends AbstractType
{
    private $listData;
    private $i;

    public function __construct($variable)
    {
        $this->listData= $variable;
        $this->i= 0;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $listDatas = $this->listData;

            if($listDatas->getName() == 'About'){
                $textType = 'ckeditor';
            }else {
                $textType = 'text';
            }
            $builder
                ->add('name', 'text', ['label_attr'=>['class'=>'hidden']])
                ->add('text','collection', [
                    'type' => new TextListType($listDatas->getText()),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'belongs_to'=>$textType, 'label'=>' ', 'label_attr'=>['class'=>'hidden']
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listDatas->getText(),
                    'label' => ' ',
                    'label_attr'=>['class'=>'hidden'],
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['class' => 'col-sm-12']])
                ->add('date','collection', [
                    'type' => new DateListType(),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'label'=>' ', 'label_attr'=>['class'=>'hidden']
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listDatas->getDate(),
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['class' => 'col-sm-12']])
                ->add('boolean','collection', [
                    'type' => new BooleanListType(),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'label'=>' ', 'label_attr'=>['class'=>'hidden']
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listDatas->getBoolean(),
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['class' => 'col-sm-12']])
                ->add('image','collection', [
                    'type' => new ImageListType(),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'label'=>' ', 'label_attr'=>['class'=>'hidden']
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listDatas->getImage(),
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['class' => 'col-sm-12']])
                ->add('file','collection', [
                    'type' => new FileListType(),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'label'=>' ', 'label_attr'=>['class'=>'hidden']
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listDatas->getFile(),
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['class' => 'col-sm-12']])
            ;
//            $this->i += 1;
//        }else{
//            $builder
//                ->add('name', 'text', ['label_attr'=>['class'=>'hidden']]);
//            $this->i += 1;
//        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\ListValues',
            'belongs_to'=>false,
                'form_data'=>null
        ));

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_page_document';
    }
}
