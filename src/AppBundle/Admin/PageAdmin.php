<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/15/17
 * Time: 12:42 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\Campaign;
use AppBundle\Entity\Page;
use AppBundle\Form\ListingListType;
use AppBundle\Form\PageDocumentsList;
use AppBundle\Form\PageValues;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\ListValues;
use AppBundle\Form\AttributesDefinitionType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Model\Metadata;

class PageAdmin extends Admin
{

    protected $em;
    protected $container;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

    }

    public function getObjectMetadata($object)
    {
        $this->em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $url = '/bundles/app/images/admin/' . $object->getSlug() . '.png';
        return new Metadata($object->getName(), '', $url);
    }


    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        // check user permissions
        if ($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {

        }

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        // get subject, is current item
        $subject = $this->getSubject();
        // get Container
        $this->container = $this->getConfigurationPool()->getContainer();

        //set entity manager for CRUD info
        $this->em = $this->container->get('doctrine')->getManager();

        $date='';

        $listValues = $this->em->getRepository('AppBundle:ListValues')->findPageData($this->getClassnameLabel(), $subject->getId());
        $collectionValues = $this->em->getRepository('AppBundle:CollectionValues')->findPageData($this->getClassnameLabel(), $subject->getId());

        $title = 'New Page';
        if(!is_null($subject->getId())){
            $subject->getCreated() ? $date = 'Created on: ' . $subject->getCreated()->format('d M Y'): $date='';
            $subject->getUpdated() ? $date .= '<br>Updated on: ' . $subject->getUpdated()->format('d M Y') : $date.='';
            $subject->getId() ? $date .= '<br>Version : ' . $subject->getId() : $date.='';
            $subject->getName() ? $title = $subject->getName() . ' Info':'';
        }

        /**
         * create form for page
         */
        $formMapper
            ->with($title, array(
                'class' => 'col-sm-12',
                'box-class' => 'box box-solid box-danger',
                'description' => $date
            ))
            ->add('name', null, ['label'=>'Page name'])
            ->add('state', 'choice', [ 'choices' =>
                array(Page::IS_ACTIVE => 'Active', Page::IS_DRAFT=> 'Draft'), 'multiple'=>false , 'label'=>'Page State'])
            ->end()
        ;
        if(!is_null($subject->getId())) {
            $formMapper
                ->with('Page Attributes', array(
                    'class' => 'col-sm-12',
                    'box-class' => 'box box-solid box-danger',
                ));
            if($this->isGranted('ROLE_SUPER_ADMIN')){
                $formMapper->add('values', 'campaign_values_type', ['label' => false])
                    ;
            }else {
                $formMapper->add('collectionList','sonata_type_native_collection', [
                    'type' => new PageDocumentsList($collectionValues),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-6 pull-left'],
                        'belongs_to'=>['name'=>$this->getClassnameLabel(), 'id'=>$subject->getId() ? $subject->getId() : null ]
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $collectionValues,
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['id'=>'sortable','class' => 'col-sm-12']])
                    ;

                $formMapper->add('values', 'sonata_type_native_collection', [
                    'type' => new PageValues($listValues),
                    'entry_options'  => [
                        'attr'      => ['class' => 'col-sm-12 pull-left'],
                        'belongs_to'=>['name'=>$this->getClassnameLabel(), 'id'=>$subject->getId() ? $subject->getId() : null ],
                    ],
                    'allow_add' => false,
                    'allow_delete' => false,
                    'cascade_validation' => false,
                    'by_reference' => false,
                    'delete_empty' => true,
                    'mapped' => true,
                    'data' => $listValues,
                    'label' => ' ',
                    'required'=>false,
                    'options' => array('label' => ' '),
                    'attr' => ['id'=>'sortable','class' => 'col-sm-12']])
                    ;
            }



            ;

            $formMapper->end();

            if($this->isGranted('ROLE_SUPER_ADMIN')){
                $formMapper
                    ->with('Settings', array(
                        'class' => 'col-sm-12',
                        'box-class' => 'box box-solid box-danger',
                        'description' => 'Settings create part'
                    ))
                    ->add('slug')
                    ->add('settings', 'sonata_type_collection', [
                        'type_options' => ['delete' => true, 'label'=>false],
                        'cascade_validation' => true, 'btn_add'=>'Add new',
                        'by_reference'       => true], [
                        'edit' => 'inline',
                        'inline' => 'table',
//                    'inline' => 'list',
                        'sortable'          => 'sortOrdering',
                        'link_parameters'   => ['context' => 'default']])
                    ->end();
            }

        }

    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {

        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $list->add('id');
        }

        $list
            ->addIdentifier('name', null, ['label'=>'Campaign name'])
            ->add('state', 'choice', [ 'choices' =>
                array(Page::IS_ACTIVE => 'Active', Page::IS_DRAFT=> 'Draft'), 'multiple'=>false , 'label'=>'Page State'])
            ->add('updated', 'date', array('date_format' => 'yyyy-MM-dd'))
            ->add('_action', 'actions',
                array('actions' =>
                    array(
                        'edit' => array(), 'delete' => array(),
//                        'project_convert' => array('template' => 'AppBundle:CRUD:project_convert.html.twig'),
//                        'project_show' => array('template' => 'AppBundle:CRUD:project_show.html.twig'))
                    )

                ));

    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {

        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $filter->add('id');
        }
        $filter
            ->add('name')
            ->add('updated', 'doctrine_orm_datetime_range', array(), 'sonata_type_datetime_range_picker',
                array('field_options_start' => array('format' => 'yyyy-MM-dd HH:mm:ss'),
                    'field_options_end' => array('format' => 'yyyy-MM-dd HH:mm:ss'))
            )
        ;
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('updated', 'date', array('date_format' => 'yyyy-MM-dd'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $em = $this->container->get('doctrine')->getManager();

        if($object->getSettings()){
            $i=0;
            foreach ($object->getSettings() as $item){
                if($item->getId() == null){
                    $item->setBelongsTo($object);
                }
                $i++;
            }
        }

        if(!is_null($object->getValues())){
            foreach ($object->getValues() as $value){
                if($value instanceof ListValues){
                    if(!$value->getFile()->isEmpty()){

                        foreach ($value->getFile() as $file){
                            $file->uploadFile();
                        }
                    }
                    if(!$value->getImage()->isEmpty()){

                        foreach ($value->getImage() as $file){
                            $file->uploadFile();
                        }
                    }
                }
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