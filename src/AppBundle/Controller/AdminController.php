<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/13/17
 * Time: 5:43 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\AttributesProjectSettings;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\ListValues;
use AppBundle\Entity\Project;
use AppBundle\Form\ListingListType;
use AppBundle\Model\ConfiguratorInterface;
use  Sonata\AdminBundle\Controller\CRUDController as Admin;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends Admin
{

    /**
     * Create action.
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {

        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract()) {
            return $this->render(
                'SonataAdminBundle:CRUD:select_subclass.html.twig',
                array(
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ),
                null,
                $request
            );
        }

        $object = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //TODO: remove this check for 4.0
            if (method_exists($this->admin, 'preValidate')) {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->checkAccess('create', $object);

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
                        ), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_create_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_create_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }


        $em = $this->getDoctrine()->getManager();


        //todo: add clone Campaign
        $campaignAll = $em->getRepository('AppBundle:Campaign')->findAll();

        if(count($campaignAll)>0){

            //get max Campaign for clone
            $campaign = $em->getRepository('AppBundle:Campaign')->findMin();

            if($object instanceof Campaign){
                $this->container->get('session')->set('cloning', 1);
                $newCampaign = clone $campaign;
                $now = new \DateTime('now');
                $newCampaign->setSortOrderDate($now);
                $this->admin->create($newCampaign);

                $collections = $em->getRepository('AppBundle:CollectionValues')->findByCmp($campaign->getId());

                foreach ($collections as $collection){

                    $coled = clone $collection;
                    $coled->setBelongsToObject($newCampaign->getId());
                    $this->admin->create($coled);

                    count($collection->getListValues())>0 ? $this->cloneListValues([$collection->getListValues()->first()], $newCampaign->getId(), $coled, AttributesDefinition::IS_CAMPAIGN):'';
                }

                //Get ListValues for current Campaign
                $values = $em->getRepository('AppBundle:ListValues')->findForThisCmp(ListValues::IS_CAMPAIGN, $campaign->getId());

                $this->cloneListValues($values, $newCampaign->getId(), null, AttributesDefinition::IS_CAMPAIGN);

                $this->addFlash('sonata_flash_success', 'Cloned successfully');
                $this->container->get('session')->set('cloning', null);
                return new RedirectResponse($this->admin->generateObjectUrl('edit', $newCampaign));

        }
        elseif ($object instanceof Project){
            $this->container->get('session')->set('cloning', 1);
                $project = $em->getRepository('AppBundle:Project')->findMin();

                $type = AttributesDefinition::IS_CAMPAIGN;
                if($project){
                    $campaign = $project;
                    $type = AttributesDefinition::IS_PROJECT;
                }
                count($project) == 0 ? $object = new Project : $object = clone $project;

            $now = new \DateTime('now');

                $newProject = new Project;
                $newProject->setName($campaign->getName());
                $newProject->setSortOrderDate($now);
                $clonedProject = $this->admin->create($newProject);

                $settings = $campaign->getSettings();

                foreach ($settings as $setting){
                    $newSetting = new AttributesProjectSettings();
                    $newSetting->setAttributesDefinition($setting->getAttributesDefinition());
                    $newSetting->setIsEnable(true);
                    $newSetting->setSortOrdering($setting->getSortOrdering());
                    $newSetting->setBelongsTo($newProject);
                    $em->persist($newSetting);
                }

                $collections = $em->getRepository('AppBundle:CollectionValues')->findByBelonbObject($type, $campaign->getId());
                $isPage = AttributesDefinition::IS_PROJECT;

                foreach ($collections as $collection){

                    $collectionClone = clone $collection;

                    $collectionClone->setBelongsToObjectName(AttributesDefinition::IS_PROJECT);
                    $collectionClone->setBelongsToObject($newProject->getId());
                    $collectionClone = $this->admin->create($collectionClone);

                    if($type == AttributesDefinition::IS_PROJECT){

                        $valuesColl = [$collection->getListValues()->first()];
                    }else {
                        $valuesColl = $collection->getListValues();
                    }
                    count($collection->getListValues())>0 ? $this->cloneListValues($valuesColl, $clonedProject->getId(), $collectionClone, $isPage):'';
                }

                //Get ListValues for current Campaign
                $values = $em->getRepository('AppBundle:ListValues')->findForThisCmp($type, $campaign->getId());

                $this->cloneListValues($values, $clonedProject->getId(), null, $isPage);

                $this->container->get('session')->set('cloning', null);
                return new RedirectResponse($this->admin->generateObjectUrl('edit', $newProject));

            }
    }

        $formView = $form->createView();
        // set the theme for the current Admin Form
        $this->setFormTheme($formView, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $formView,
            'object' => $object,
        ), null);
    }

    /**
     * @return RedirectResponse
     */
    public function cloneAction($state = null)
    {
        $object = $this->admin->getSubject();
        $em = $this->getDoctrine()->getManager();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }
        $belongsToObject = $object->getBelongsToObject();
        // Be careful, you may need to overload the __clone method of your object
        // to set its id to null !
        $clonedObject = clone $object;
        $this->admin->setSubject($clonedObject);
        if($object instanceof ListValues){
            $state ? $clonedObject->setLabel($state) : '';
            $clonedObject->setBelongsToObject($belongsToObject);
            $attributesDefinitionData = $em->getRepository('AppBundle:AttributesDefinition')->findByObjectType(AttributesDefinition::IS_LIST, $object->getId());
        }
        $this->admin->create($clonedObject);


        if(count($attributesDefinitionData)){
            foreach ($attributesDefinitionData as $attDef){
                $clonedAttrDef = clone $attDef;
                $clonedAttrDef->setBelongsToObject($clonedObject->getId());
                $this->admin->create($clonedAttrDef);
            }
        }

        if(!$state){
            $this->addFlash('sonata_flash_success', 'Cloned successfully');

            return new RedirectResponse($this->admin->generateObjectUrl('list', $clonedObject));
        }else {
            $this->admin->setSubject($clonedObject);
            return $clonedObject->getId();
        }

    }

    public function projectConvertAction($campaignId){

        $em = $this->getDoctrine()->getManager();
        $campaign = $em->getRepository('AppBundle:Campaign')->find((int)$campaignId);

        if ($campaign->getState() == 0){
            $this->container->get('session')->set('cloning', 1);
            $project = null;

            $type = AttributesDefinition::IS_CAMPAIGN;
            if($project){
                $campaign = $project;
                $type = AttributesDefinition::IS_PROJECT;
            }
            count($project) == 0 ? $object = new Project : $object = clone $project;

            $newProject = new Project;
            $newProject->setName($campaign->getName());
            $newProject->setState(Project::IS_ACTIVE);
            $newProject->setVersion(1);
            $clonedProject = $this->admin->create($newProject);

            $settings = $campaign->getSettings();

            $disabled = ['Sidebar Title', 'Sidebar', 'Fundraiser ideas', 'Fundraiser Ideas Button'];
            $isEnabled = true;
            foreach ($settings as $setting){
                if(in_array($setting->getAttributesDefinition()->getAttrName(), $disabled)){
                    $isEnabled = false;
                }
                $newSetting = new AttributesProjectSettings();
                $newSetting->setAttributesDefinition($setting->getAttributesDefinition());
                $newSetting->setIsEnable($isEnabled);
                $newSetting->setSortOrdering($setting->getSortOrdering());
                $newSetting->setBelongsTo($newProject);
                $em->persist($newSetting);
            }

            $collections = $em->getRepository('AppBundle:CollectionValues')->findByBelonbObject($type, $campaign->getId());

            $isPage = AttributesDefinition::IS_PROJECT;

            foreach ($collections as $collection){

                $collectionClone = clone $collection;

                $collectionClone->setBelongsToObjectName(AttributesDefinition::IS_PROJECT);
                $collectionClone->setBelongsToObject($newProject->getId());
                $collectionClone = $this->admin->create($collectionClone);

                if($type == AttributesDefinition::IS_PROJECT){

                    $valuesColl = [$collection->getListValues()->first()];
                }else {
                    $valuesColl = $collection->getListValues();
                }
                count($collection->getListValues())>0 ? $this->cloneListValues($valuesColl, $clonedProject->getId(), $collectionClone, $isPage, true):'';
            }

            //Get ListValues for current Campaign
            $values = $em->getRepository('AppBundle:ListValues')->findForThisCmp($type, $campaign->getId());

            $this->cloneListValues($values, $clonedProject->getId(), null, $isPage, true);

            $campaign->setState(Campaign::IS_COMPLETED);
            $em->persist($campaign);
            $em->flush();
            $this->container->get('session')->set('cloning', null);
            $this->addFlash('sonata_flash_success', "{$campaign->getName()} Converted to project  successfully");
            return $this->redirectToRoute('admin_app_project_edit', ['id'=>$newProject->getId()]);
        }else {
            $this->addFlash('sonata_flash_error', "{$campaign->getName()} Can't converted to project, it`s Draft");
            return $this->redirectToRoute('admin_app_campaign_list', ['id'=>$campaign->getId()]);
        }
    }

    public function saveDraftAction($campaignId){

        $objectClass = $this->admin->getClassnameLabel();
        $em = $this->getDoctrine()->getManager();
        $campaign = $em->getRepository('AppBundle:'.$objectClass)->find((int)$campaignId);
        $now = new \DateTime('now');

        if ($campaign->getState() != Campaign::IS_DRAFT){

            $this->container->get('session')->set('cloning', 1);
            //todo: create Draft project
            if($objectClass == AttributesDefinition::IS_PROJECT){

                $project = null;

                $type = $objectClass;
                if($project){
                    $campaign = $project;
//                    $type = AttributesDefinition::IS_PROJECT;
                }
                count($project) == 0 ? $object = new Project : $object = clone $project;

                $newProject = new Project;
                $newProject->setSlug($campaign->getSlug() . '-' . $campaign->getId());
                $newProject->setTempSlug($campaign->getSlug() . md5($now->format('Y-m-d')));
                $newProject->setState(Project::IS_DRAFT);
                $newProject->setName('Draft-'.$campaign->getName());
                $newProject->setVersion($campaign->getVersion());
                $newProject->setCreated($campaign->getCreated());
                $newProject->setUpdated($campaign->getUpdated());
                $clonedProject = $this->admin->create($newProject);

                $settings = $campaign->getSettings();

                $disabled = ['Sidebar Title', 'Sidebar', 'Fundraiser ideas', 'Fundraiser Ideas Button'];
                $isEnabled = true;

                foreach ($settings as $setting){

                    if(in_array($setting->getAttributesDefinition()->getAttrName(), $disabled)){
                        $isEnabled = false;
                    }

                    $newSetting = new AttributesProjectSettings();
                    $newSetting->setAttributesDefinition($setting->getAttributesDefinition());
                    $newSetting->setIsEnable($isEnabled);
                    $newSetting->setSortOrdering($setting->getSortOrdering());
                    $newSetting->setBelongsTo($newProject);
                    $em->persist($newSetting);
                }

                $collections = $em->getRepository('AppBundle:CollectionValues')->findByBelonbObject($type, $campaign->getId());

                $isPage = AttributesDefinition::IS_PROJECT;

                foreach ($collections as $collection){

                    $collectionClone = clone $collection;

                    $collectionClone->setBelongsToObjectName(AttributesDefinition::IS_PROJECT);
                    $collectionClone->setBelongsToObject($newProject->getId());
                    $collectionClone = $this->admin->create($collectionClone);

                    if($type == AttributesDefinition::IS_PROJECT){

                        $valuesColl = [$collection->getListValues()->first()];
                    }else {
                        $valuesColl = $collection->getListValues();
                    }

                    count($collection->getListValues())>0 ? $this->cloneListValues($valuesColl, $clonedProject->getId(), $collectionClone, $isPage, true):'';
                }

                //Get ListValues for current Campaign
                $values = $em->getRepository('AppBundle:ListValues')->findForThisCmp($type, $campaign->getId());

                $this->cloneListValues($values, $clonedProject->getId(), null, $isPage, true);

                $em->flush();
                $this->container->get('session')->set('cloning', null);
                $this->addFlash('sonata_flash_success', "{$campaign->getName()} Copy  successfully");
                return $this->redirectToRoute('admin_app_project_edit', ['id'=>$newProject->getId()]);
            }elseif ($objectClass == AttributesDefinition::IS_CAMPAIGN){


                $this->container->get('session')->set('cloning', 1);
                $newCampaign = clone $campaign;
                $newCampaign->setSlug($campaign->getSlug() . '-' . $campaign->getId());
                $newCampaign->setTempSlug($campaign->getSlug() . md5($now->format('Y-m-d')));
                $newCampaign->setState(Campaign::IS_DRAFT);
                $newCampaign->setName('Draft-'.$campaign->getName());
                $newCampaign->setCreated($campaign->getCreated());
                $newCampaign->setUpdated($campaign->getUpdated());
                $this->admin->create($newCampaign);

                $collections = $em->getRepository('AppBundle:CollectionValues')->findByCmp($campaign->getId());

                foreach ($collections as $collection){

                    $coled = clone $collection;
                    $coled->setBelongsToObject($newCampaign->getId());
                    $this->admin->create($coled);

                    count($collection->getListValues())>0 ? $this->cloneListValues($collection->getListValues(), $newCampaign->getId(), $coled, AttributesDefinition::IS_CAMPAIGN):'';
                }

                //Get ListValues for current Campaign
                $values = $em->getRepository('AppBundle:ListValues')->findForThisCmp(ListValues::IS_CAMPAIGN, $campaign->getId());

                $this->cloneListValues($values, $newCampaign->getId(), null, AttributesDefinition::IS_CAMPAIGN);

                $this->addFlash('sonata_flash_success', "{$campaign->getName()} Copy successfully");
                $this->container->get('session')->set('cloning', null);
                return new RedirectResponse($this->admin->generateObjectUrl('edit', $newCampaign));

            }

        }else {
            $this->addFlash('sonata_flash_error', "{$campaign->getName()} Can't converted to project, it`s Draft");
            return $this->redirectToRoute('admin_app_campaign_list', ['id'=>$campaign->getId()]);
        }
    }

    /**
     * Redirect the user depend on this choice.
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        $url = false;

        if (null !== $request->get('btn_update_and_list')) {
            $url = $this->admin->generateUrl('list');
        }
        if (null !== $request->get('btn_create_and_list')) {
            $url = $this->admin->generateUrl('list');
        }

        if (null !== $request->get('btn_save_as_draft')) {

            return $this->saveDraftAction($object->getId());

        }

        if (null !== $request->get('btn_save_as')) {

            return $this->saveAs($object);

        }

        if (null !== $request->get('btn_create_and_create')) {
            $params = array();
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if ($this->getRestMethod() === 'DELETE') {
            $url = $this->admin->generateUrl('list');
        }

        if (!$url) {
            foreach (array('edit', 'show') as $route) {
                if ($this->admin->hasRoute($route) && $this->admin->isGranted(strtoupper($route), $object)) {
                    $url = $this->admin->generateObjectUrl($route, $object);
                    break;
                }
            }
        }

        if (!$url) {
            $url = $this->admin->generateUrl('list');
        }

        if(null != $request->get('btn_update_and_edit_list_value')){

            foreach ($request->get('btn_update_and_edit_list_value') as $kay=>$value){
                return $this->redirectToRoute('admin_app_listvalues_edit', array('id'=>$kay));
            }
        }

        return new RedirectResponse($url);
    }

    private function cloneListValues($values, $clonedId, $collection = null, $isPage = null, $isConvert = null){
        //Clone ListValues and AttributesDefinitions for current ListValues
        $logInfo = $this->container->get('monolog.logger.process_error');
        $fs = new Filesystem();

        foreach ($values as $value){

            $clonedVal = clone $value;
            $dir = str_replace('/app', '/web', $this->container->getParameter('kernel.root_dir'));

            if(!$clonedVal->getImage()->isEmpty() && $isConvert != null){

                foreach ($clonedVal->getImage() as $image){


                    $idsToFilter = array($image->getTitle());

                    $imgByName = $value->getImage()->filter(

                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getTitle(), $idsToFilter);
                        }
                    );

                    try {
                        $oldImage = $imgByName->first();
                        if(strlen($oldImage->getDownloadLink()) >0 && $fs->exists($dir.$oldImage->getDownloadLink()) === true) {

                            $image->setFileOriginalName($oldImage->getFileOriginalName());
                            $path_parts = pathinfo($dir.$oldImage->getDownloadLink());
                            // generate filename
                            $newFilename = md5(microtime()) . '.' . $path_parts['extension'];
                            $image->setFileName($newFilename);
                            $fs->copy($dir.$oldImage->getDownloadLink(), $dir .'/uploads/files/'.$newFilename, true);
                            $fs->chown($dir .'/uploads/files/'.$newFilename, 'www-data', true);
                        }
                    } catch (\Exception $e) {
                        $message = json_encode($e->getMessage());
                        $logInfo->addInfo("Copy to Draft Image can`t upload: : $message\r\n");
                    }
                }
            }

            if(!$clonedVal->getFile()->isEmpty() && $isConvert != null){

                foreach ($clonedVal->getFile() as $file){


                    $idsToFilter = array($file->getTitle());

                    $fileByName = $value->getFile()->filter(

                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getTitle(), $idsToFilter);
                        }
                    );
                    try {
                        $oldFile = $fileByName->first();
                        if(strlen($oldFile->getDownloadLink()) >0 && $fs->exists($dir.$oldFile->getDownloadLink()) === true){
                            $file->setFileOriginalName($oldImage->getFileOriginalName());
                            $path_parts = pathinfo($dir.$oldImage->getDownloadLink());
                            // generate filename
                            $newFilename = md5(microtime()) . '.' . $path_parts['extension'];
                            $file->setFileName($newFilename);
                            $fs->copy($dir.$oldImage->getDownloadLink(), $dir .'/uploads/files/'.$newFilename, true);
                            $fs->chown($dir .'/uploads/files/'.$newFilename, 'www-data', true);
                        }
                    } catch (\Exception $e) {
                        $message = json_encode($e->getMessage());
                        $logInfo->addInfo("Copy to Draft File can`t upload : $message\r\n");
                    }
                }
            }

            if($collection) {
                $clonedVal->setBelongsToObjectName(AttributesDefinition::IS_COLLECTION);
            } else{
                $clonedVal->setBelongsToObjectName($isPage);
            }

            $clonedVal->setBelongsToObject($clonedId);
            $clonedVal->setCollectionValues($collection);
            $this->admin->create($clonedVal);

        }
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     *
     * @param FormView $formView
     * @param string   $theme
     */
    private function setFormTheme(FormView $formView, $theme)
    {
        $twig = $this->get('twig');

        try {
            $twig
                ->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')
                ->setTheme($formView, $theme);
        } catch (\Twig_Error_Runtime $e) {

            $twig
                ->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')
                ->renderer
                ->setTheme($formView, $theme);
        }
    }

    public function saveAs($object){

        if($object instanceof  ConfiguratorInterface){

            $em = $this->getDoctrine()->getManager();
            $data = explode('-', $object->getSlug());
            $id = (int)$data[count($data)-1];
            $old = $em->getRepository($object->getClassName())->find($id);

            $oldData = $old;

            $em->remove($old);
            $em->flush();

            $object->setName($old->getName());
            $object->setState($old->getState());
            $object->setSlug($old->getSlug());
            $object->setVersion($old->getVersion() + 1);

            $em->persist($object);

            $em->flush();

            $url = $this->admin->generateUrl('list');

            return new RedirectResponse($url);
        }
    }

}