<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/15/17
 * Time: 12:42 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\AttributesDefinition;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AttributesDefinitionAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Main',  ['class' => 'col-sm-6',
                        'box-class' => 'box box-solid box-danger',
                        'description' => 'Parent Info create part'])
                ->add('attrName', null, ['label'=>'Attr Name'])
                ->add('attrClass', 'choice', array('choices' =>
                    [   AttributesDefinition::IS_IMAGE => 'Image',
                        AttributesDefinition::IS_FILE=> 'File',
                        AttributesDefinition::IS_TEXT=> 'Text',
                        AttributesDefinition::IS_DATE=> 'Date',
                        AttributesDefinition::IS_LIST=> 'Listing',
                        AttributesDefinition::IS_BOOL=> 'Boolean',
                    ],
                    'multiple' => false
                ), ['required' => false, 'label'=>'Attr Class'])
                ->add('isRequired')
                ->add('isPublic')
            ->end();

    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
//            ->addIdentifier('objectName')
            ->add('attrName')
            ->addIdentifier('attrClass', 'choice', array(
                'choices' =>
                    [   AttributesDefinition::IS_IMAGE => 'Image',
                        AttributesDefinition::IS_FILE=> 'File',
                        AttributesDefinition::IS_TEXT=> 'Text',
                        AttributesDefinition::IS_LIST=> 'Listing',
                    ],
            ))
            ->add('isRequired')
//            ->add('moderatorSortOrder')
            ->add('isPublic')
//            ->add('belongsToObject')
            ->add('_action', 'actions',
                array('actions' =>
                    array(
                        'show' => array(), 'edit' => array(), 'delete' => array())
                ));

    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
//            ->add('objectName')
            ->add('attrName')
            ->add('attrClass', 'doctrine_orm_choice', [],
                'choice', array(
                'choices' =>
                    [   AttributesDefinition::IS_IMAGE => 'Image',
                        AttributesDefinition::IS_FILE=> 'File',
                        AttributesDefinition::IS_TEXT=> 'Text',
                        AttributesDefinition::IS_LIST=> 'Listing',
                    ],
            ))
            ->add('isRequired')
//            ->add('moderatorSortOrder')
            ->add('isPublic')
//            ->add('belongsToObject')
          ;
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
//            ->add('objectName')
            ->add('attrName')
            ->add('attrClass', 'doctrine_orm_choice', [],
                'choice', array(
                    'choices' =>
                        [   AttributesDefinition::IS_IMAGE => 'Image',
                            AttributesDefinition::IS_FILE=> 'File',
                            AttributesDefinition::IS_TEXT=> 'Text',
                            AttributesDefinition::IS_LIST=> 'Listing',
                        ],
                ))
            ->add('isRequired')
            ->add('moderatorSortOrder')
            ->add('isPublic')
           ;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
//        $object->uploadFile();
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
//        $object->uploadFile();
    }
}