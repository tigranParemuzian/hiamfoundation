<?php

namespace AppBundle\Form;

use AppBundle\Entity\AttributesDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributesDefinitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->belongsTo = $options['belongs_to'];
        $builder
            ->add('isPublic', 'hidden', ['required'=>false, 'label'=>'Object name', 'data'=>true])
            ->add('attrName', 'text', ['required'=>true, 'label'=>'Attr Name']);

        if($this->belongsTo != 'AttributesListSettings' ){

            $builder
                ->add('attrClass', 'choice', ['choices'=>[
                        AttributesDefinition::IS_LIST=> 'Document',
                        AttributesDefinition::IS_COLLECTION=> 'Documents List',
                    ]
                ],
                ['required'=>true, 'label'=>'Attr Name']);

        }else{

            $builder->add('attrClass', 'choice', ['choices'=>[
                AttributesDefinition::IS_TEXT=> 'Text',
                AttributesDefinition::IS_DATE=> 'Date',
                AttributesDefinition::IS_IMAGE => 'Image',
                AttributesDefinition::IS_FILE=> 'File',
                AttributesDefinition::IS_BOOL=> 'Boolean'
            ]
            ],
                ['required'=>true, 'label'=>'Attr Name']);
        }
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\AttributesDefinition',
            'belongs_to'=>false
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_attributes_definition_type';
    }
}
