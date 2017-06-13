<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/15/17
 * Time: 12:42 PM
 */

namespace AppBundle\Admin;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\Project;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\ListValues;
use AppBundle\Form\AttributesDefinitionType;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Model\Metadata;

class ProjectAdmin extends Admin
{

    protected $em;
    protected $container;

    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC', // sort direction
        '_sort_by' => 'id' // field name
    );

   public function getObjectMetadata($object)
    {
        $this->em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();

        $url = '/bundles/app/img/onearmenia-top-nav-logo.png';

       $data = $this->em->getRepository('AppBundle:ListValues')->findForViuew(AttributesDefinition::IS_PROJECT, $object->getId(), 'Header');
       $about = $this->em->getRepository('AppBundle:ListValues')->findForViuew(AttributesDefinition::IS_PROJECT, $object->getId(), 'About');

        $message = '<p>';

       if($data[0]->getImage()->first()){
           $url = $data[0]->getImage()->first()->getDownloadLink();
       }

       if(!$about[0]->getDate()->isEmpty()){
           foreach ($about[0]->getDate() as $date) {
               if(strpos($date->getSlug(),'started-date') !==false &&  !is_null($date->getValue())){
                   $stDt = 'Started at &nbsp;' . $date->getValue()->format('d M Y');
               }
               if(strpos($date->getSlug() ,'end-date') !==false && !is_null($date->getValue())){
                   $endDt = 'End at &nbsp;' . $date->getValue()->format('d M Y');
               }
           }
       }
        isset($stDt) ? $message .= $stDt . '<br>': '';
        isset($endDt) ? $message .= $endDt : '';
        $message .= '</p>';
       return new Metadata($object->getName(), $message, $url);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('project_show');
        $collection->add('save_draft', 'save/draft/{campaignId}');
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        // check user permissions
        if ($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')) {
            // define calculate action
            $actions['save_draft'] = array('label' => 'Save Draft', 'ask_confirmation' => true);
            $actions['project_show'] = array('label' => 'Show Project', 'ask_confirmation' => true);
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

        $state ='Active';

        if(!is_null($subject->getId())){
            $subject->getState() === Project::IS_DRAFT ? $state = 'Draft' : '';

            $subject->getCreated() ? $date = 'Created on: ' . $subject->getCreated()->format('d M Y'): $date='';
            $subject->getUpdated() ? $date .= '<br>Updated on: ' . $subject->getUpdated()->format('d M Y') : $date.='';
            $subject->getId() ? $date .= '<br>Version : ' . $subject->getVersion() : $date.='';
            $subject->getId() ? $date .= '<br>State : ' . $state : $date.='';
        }

        /**
         * create form for page
         */
        $formMapper
            ->with('Project Info', array(
                'class' => 'col-sm-12',
                'box-class' => 'box box-solid box-danger',
                'description' => $date
            ))
            ->add('name', null, ['label'=>'Name'])
            ->add('goal', null, ['label'=>'Goal'])
            ->add('rest', null, ['label'=>'Raised']);
        if($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper->
            add('state', 'choice', ['choices' =>
                array(Project::IS_ACTIVE => 'Active', Project::IS_DRAFT => 'Draft',
                    Project::IS_COMPLETED => 'Completed'
                ), 'multiple' => false, 'label' => 'State']);
        }
        $formMapper->end()
        ;
        if(!is_null($subject->getId())) {
            $formMapper
                ->with('Project Attributes', array(
                    'class' => 'col-sm-12',
                    'box-class' => 'box box-solid box-danger',
                ))
                ->add('values', 'campaign_values_type', ['label' => false])
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
            ->addIdentifier('name', null, ['label'=>'Project name'])
            ->add('state', 'choice', [ 'choices' =>
                array(Project::IS_ACTIVE => 'Active', Project::IS_DRAFT=> 'Draft',
                    Project::IS_COMPLETED=> 'Active'), 'multiple'=>false , 'editable'=>false, 'label'=>'Project State'])
            ->add('updated', 'date', array('date_format' => 'yyyy-MM-dd'));
        if($this->isGranted('ROLE_SUPER_ADMIN')){

            $list->add('sortOrderDate', 'date', array('date_format' => 'yyyy-MM-dd'));
        }
            $list->add('_action', 'actions',
                array('actions' =>
                    array(
                        'edit' => array(), 'delete' => array(),
                        'save_draft' => array('template' => 'AppBundle:CRUD:save_draft.html.twig'),
                        'project_show' => array('template' => 'AppBundle:CRUD:project_show.html.twig'))

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
            ->add('state', 'doctrine_orm_choice', array(),
                'choice', array('choices'  =>  array(Project::IS_ACTIVE => 'Active', Project::IS_DRAFT=> 'Draft'))
            )
            ->add('updated', 'doctrine_orm_datetime_range', array(), 'sonata_type_datetime_range_picker',
                array('field_options_start' => array('format' => 'yyyy-MM-dd HH:mm:ss'),
                    'field_options_end' => array('format' => 'yyyy-MM-dd HH:mm:ss'))
            )
        ;

        if($this->isGranted('ROLE_SUPER_ADMIN')){

            $filter->add('sortOrderDate', 'doctrine_orm_datetime_range', array(), 'sonata_type_datetime_range_picker',
                array('field_options_start' => array('format' => 'yyyy-MM-dd HH:mm:ss'),
                    'field_options_end' => array('format' => 'yyyy-MM-dd HH:mm:ss'))
            );
        }
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
        if($object->getSettings()){
            $i=0;
            foreach ($object->getSettings() as $item){
                if($item->getId() == null){
                    $item->setBelongsTo($object);
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