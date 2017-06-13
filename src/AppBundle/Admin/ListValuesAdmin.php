<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/15/17
 * Time: 12:42 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\ListValues;
use AppBundle\Form\AttributesDefinitionType;
use AppBundle\Form\DateListType;
use AppBundle\Form\FileListType;
use AppBundle\Form\ImageListType;
use AppBundle\Form\ParentInfoListType;
use AppBundle\Form\TextListType;
use AppBundle\Form\BooleanListType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ListValuesAdmin extends Admin
{

    protected $container;
    protected $settigs;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

    protected function configureRoutes(RouteCollection $collection)
    {

        $collection->add('clone', $this->getRouterIdParameter().'/clone');

    }

    protected function configureFormFields(FormMapper $formMapper)
    {


        $this->container =$this->getConfigurationPool()->getContainer();
        //set entity manager for CRUD info
        $em = $this->container->get('doctrine')->getManager();
        $item = $this->getSubject();

        if($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Main');
        }
        $formMapper
            ->with($item->getName(),  ['class' => 'col-sm-12',
                        'box-class' => 'box box-header box-danger',
                        'description' => ' '])
            ;

        if($this
            ->isGranted('ROLE_SUPER_ADMIN')){
            $formMapper                ->add('name')
            ;
        }

        if($item->getName() == 'About' || (!is_null($item->getCollectionValues()) && $item->getCollectionValues()->getName() =='Interviewers')
        ){
            $textType = 'ckeditor';
        }elseif($item->getName() == 'Category' ) {
            $textType = 'choice';
        }else {
            $textType = 'text';
        }

        if(!$item->getText()->isEmpty()){

            $formMapper->add('text','sonata_type_native_collection', [
                'type' => new TextListType($item->getText()),
                'entry_options'  => [
                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                    'belongs_to'=>$textType
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $item->getText(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['class' => 'col-sm-12']]);
        }
        if(!$item->getBoolean()->isEmpty()){

            $bl =[];
            if(!is_null($item->getCollectionValues()) && $item->getCollectionValues()->getName() == 'Interviewers' && $item->getCollectionValues()->getBelongsToObjectName() == AttributesDefinition::IS_PAGE){
                $bl['label'] = 'Have interview';
            }

            $formMapper->add('boolean','collection', [
                'type' => new BooleanListType(),
                'entry_options'  => [
                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                    'belongs_to'=>$bl
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $item->getBoolean(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['class' => 'col-sm-12']]);
        }

        if(!$item->getDate()->isEmpty()){

            $formMapper->add('date','collection', [
                'type' => new DateListType(),
                'entry_options'  => [
                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $item->getDate(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['class' => 'col-sm-12']]);
        }

        if(!$item->getImage()->isEmpty()){

            $formMapper->add('image', 'collection', [
                'type' => new ImageListType(),
                'entry_options'  => [
//                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $item->getImage(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['class' => 'col-sm-12']]);

        }

        if(!$item->getFile()->isEmpty()){

            $formMapper->add('file', 'collection', [
                'type' => new FileListType(),
                'entry_options'  => [
//                    'attr'      => ['class' => 'col-sm-6 pull-left'],
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'cascade_validation' => false,
                'by_reference' => false,
                'delete_empty' => true,
                'mapped' => true,
                'data' => $item->getFile(),
                'label' => ' ',
                'required'=>false,
                'options' => array('label' => ' '),
                'attr' => ['class' => 'col-sm-12']]);

        }

        $formMapper->end();

        if($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper->end()
            ->tab('Settings')
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
                ->end()
                ->end();
        }
            //todo: showed part
       /* $formMapper
            ->tab('Item info')
            ->with('Settings', array(
                'class' => 'col-sm-12',
                'box-class' => 'box box-solid box-danger',
                'description' => 'Settings create part'
            ))*/
           ;
           /* $formMapper->end()
            ->end()*/
            ;

    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->add('name');
        if($this->isGranted('ROLE_SUPER_ADMIN')) {
            $list->add('slug');
        }
        $list->add('text')
            ->add('date')
            ->add('belongsToObjectName')
            ->add('belongsToObject')
            ->add('_action', 'actions',
                array('actions' =>
                    array(
                        'clone' => ['template' => 'AppBundle:CRUD:list__action_clone.html.twig'],
                        'show' => [], 'edit' => [], 'delete' => [])
                ));

    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('id')
            ->add('name');
            if($this->isGranted('ROLE_SUPER_ADMIN')) {
                $filter->add('slug');
            }
        $filter->add('text')
        ->add('belongsToObjectName')
        ->add('belongsToObject')
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
            ->add('text')
            ->add('belongsToObjectName')
            ->add('belongsToObject')
           ;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        if($object->getSettings()) {
            foreach ($object->getSettings() as $itemSetting){
                if($itemSetting->getBelongsTo() == null){
                    $itemSetting->setBelongsTo($object);
                }
            }
        }

         if($object->getImage()){
             foreach ($object->getImage() as $item) {
                 $item->uploadFile();
             }
         }

         if($object->getFile()){
             foreach ($object->getFile() as $item) {
                 $item->uploadFile();
             }
         }
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        /*if($this->getConfigurationPool()->getContainer()->get('request')->get('_route') != 'admin_app_listvalues_clone'){

            $this->container->get('app.calculate.list.values')->checkAttributes($object, $this->settigs);
        }*/
    }
}