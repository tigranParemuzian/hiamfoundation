<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/15/17
 * Time: 12:42 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Form\AttributesDefinitionType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AttributesCampaignSettingsAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC', // sort direction
        '_sort_by' => 'sortOrdering' // field name
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Main',  ['class' => 'col-sm-6',
                        'box-class' => 'hidden',
                        'description' => 'Parent Info create part'])
            ->add('sortOrdering', 'hidden')
//            ->add('belongsTo')
            ->add('isEnable', null, ['required'=>false])
            ->add('attributesDefinition', AttributesDefinitionType::class, ['belongs_to'=>$this->getClassnameLabel()])
            ->end();

    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->addIdentifier('belongsTo.name')
            ->add('attributesDefinition')
            ->add('sortOrdering', 'hidden')
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
            ->add('sortOrdering')
//            ->add('isEnable')
            ->add('attributesDefinition')
          ;
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('sortOrdering', 'hidden')
//            ->add('isEnable', 'hidden')
            ->add('attributesDefinition')
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