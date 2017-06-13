<?php
/**
 * Created by PhpStorm.
 * User: parem
 * Date: 1/17/17
 * Time: 1:11 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\Item;
use AppBundle\Entity\PageSettings;
use AppBundle\Form\ListingListType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class CollectionValuesAdmin extends Admin
{
    private $container;
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC', // sort direction
        '_sort_by' => 'id' // field name
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->container =$this->getConfigurationPool()->getContainer();
        $item = $this->getSubject();
        /**
         * get tabs by page settings
         */
            $formMapper
                ->with($item->getName(), array(
                    'class' => 'col-sm-12',
                    'box-class' => 'box box-solid box-danger',
                    'description' => ' '
                ));
//TODO only admin                ->add('label')
                    /*if($this->isGranted('ROLE_SUPER_ADMIN')) {
                        $formMapper
                            ->add('belongsToObjectName')
                            ->add('belongsToObject')
                            ;
                    }*/
        $formMapper->end();

                $formMapper
                    ->with($item->getName(), array(
                        'class' => 'col-sm-12',
                        'box-class' => 'box box-solid box-danger',
                        'description' => ' '
                    ))

                    ->add('listValues','sonata_type_native_collection', [
                        'type' => new ListingListType(),
                        'entry_options'  => [
                            'attr'      => ['class' => 'col-sm-6 pull-left'],
                            'belongs_to'=>['name'=>$this->getClassnameLabel(), 'id'=>$item->getId() ? $item->getId() : null ]
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'cascade_validation' => false,
                        'by_reference' => false,
                        'delete_empty' => true,
                        'mapped' => true,
                        'data' => $item->getListValues(),
                        'label' => ' ',
                        'required'=>false,
                        'options' => array('label' => ' '),
                        'attr' => ['id'=>'sortable','class' => 'col-sm-12']])
                ;
        ;
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->add('name')
            ->add('belongsToObjectName')
            ->add('belongsToObject')
            ->add('listValues')

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
            ->add('name')
            ->add('belongsToObjectName')
            ->add('belongsToObject')
            ->add('listValues')

        ;
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('id')
            ->add('name')
            ->add('belongsToObjectName')
            ->add('belongsToObject')
            ->add('listValues')
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        if($object->getListValues()){
            $i = 0;
            foreach ($object->getListValues() as $elem){

                if($elem->getId() == null){

                    $elem->setBelongsToObjectName(AttributesDefinition::IS_COLLECTION);
                    $elem->setBelongsToObject((int)$object->getBelongsToObject());
                    $elem->setCollectionValues($object);
                    $elem->getSortOrdering() == null? $elem->setSortOrdering($i): '';
                    $this->container->get('app.calculate.list.values')->checkAttributes($elem, []);
                }

                $i++;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {

    }
}