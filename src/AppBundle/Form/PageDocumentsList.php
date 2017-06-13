<?php

namespace AppBundle\Form;

use AppBundle\Entity\AttributesDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageDocumentsList extends AbstractType
{
    private $listValues;
    private $i;
    private $belongsToObjectName;
    private $belongsToObject;


    public function __construct($listValues)
    {
        $this->listValues = $listValues;
        $this->i = 0;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $list = $this->listValues[$this->i];
        $this->belongsToObject = $list->getId();
        $this->belongsToObjectName = AttributesDefinition::IS_COLLECTION;

        $builder
            ->add('name', 'text_simple_type', ['label_attr'=>['class'=>'hidden']])
            ->add('listValues','sonata_type_native_collection', [
                'type' => new ListingListType(),
                'entry_options'  => [
                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                    'belongs_to'=>['name'=>$this->belongsToObjectName, 'id'=>$this->belongsToObject ? $this->belongsToObject : null, 'isEdit'=>true, 'collection'=>$list ]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $list->getListValues(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['id'=>'sortable','class' => 'col-sm-12']])
            ;
        $this->i++;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\CollectionValues',
            'belongs_to'=>false,
            'form_data'=>null
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_page_documents_list';
    }
}
