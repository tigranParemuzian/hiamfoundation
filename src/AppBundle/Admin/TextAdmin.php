<?php
/**
 * Created by PhpStorm.
 * User: parem
 * Date: 1/17/17
 * Time: 1:11 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Image;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class TextAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
//            ->tab('Main')
            ->with(' ', array(
                'class' => 'col-sm-12',
                'box-class' => 'box box-solid box-danger',
                'description' => 'Settings create part'
            ))
            ->add('title', 'text', ['required' => true])
            ->add('value', 'text', ['required' => true])
            ->add('formType', 'text', ['required' => false])
            ->end();

    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->addIdentifier('title')
            ->addIdentifier('value')
            ->addIdentifier('belongsToObject.name')
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
            ->add('title')
            ->add('value')
            ->add('belongsToObject.name');
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('value');
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