<?php

namespace AppBundle\Command;

use AppBundle\Entity\AttributesCampaignSettings;
use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\AttributesListSettings;
use AppBundle\Entity\AttributesProjectSettings;
use AppBundle\Entity\BooleanValues;
use AppBundle\Entity\Image;
use AppBundle\Entity\ListValues;
use AppBundle\Entity\TextValues;
use AppBundle\Model\AttributabeleInterface;
use AppBundle\Model\ConfiguratorInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:migration_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->checkProjectOrdering($output);

        exit;

        $this->updateTextValueFormType('Interviewers', 'Page', 'About Interviewer', 'ckeditor');
        exit;

        $em = $this->getContainer()->get('doctrine')->getManager();

        $texts = $em->getRepository('AppBundle:TextValues')->findBy(['title'=>'Select Category', 'value'=>'Agriculture']);

        foreach ($texts as $text){
            $text->setValue('Community');
            $em->persist($text);
        }

        $em->flush();

        $this->removeAttribute(AttributesDefinition::IS_PROJECT, AttributesDefinition::IS_LIST, 'About', 'Hover text');
        $this->removeAttribute(AttributesDefinition::IS_CAMPAIGN, AttributesDefinition::IS_LIST, 'About', 'Hover text');
        exit;
        $this->updateAttributesOrdering('About', AttributesDefinition::IS_PROJECT);
        $this->updateTextValueFormType('Interviewers', 'Page', 'About Interviewer', 'ckeditor');
        exit;

        /**
         * todo: project sorting
         */
        $dates = $em ->getRepository('AppBundle:DateValues')->findByOrderDate('Start Date');
        $cnt = count($dates);
        $ides = [];
        foreach ($dates as $date){

            if(!is_null($date->getBelongsToObject())){
                $output->writeln("<info>Update {$date->getTitle()} </info>");
                $t = $date->getBelongsToObject()->getBelongsToObject();
                $ides[] = $t;
                $project = $em->getRepository('AppBundle:Project')->find((int)$t);

                if($project){
                    $project->setSortOrderDate($date->getValue());
                    $em->persist($project);
                }
            }
        }
        $em->flush();
        $output->writeln("<info>Finish {$cnt} item </info>");

//        $page = $em->getRepository('AppBundle:Page')->findForShow('home');
        $list = $em->getRepository('AppBundle:ListValues')->findOneBy(['slug'=>'documents']);

        foreach ($list->getSettings() as $setting){

            $idsToFilter = array($setting->getAttributesDefinition()->getAttrName());

            $texts = $list->getText()->filter(

                function($entry) use ($idsToFilter) {
                    return in_array($entry->getTitle(), $idsToFilter);
                }
            );

            if(!$texts->isEmpty()){
                $texts->first()->setSortOrdering($setting->getSortOrdering());
                $em->persist($texts->first());
            }
        }

$em->flush();


        exit;

        /**
         *
         */
        $this->updateTextValue('About', 'Campaign', 'About Description', 'ckeditor');
        $this->updateTextValue('About', 'Project', 'About', 'ckeditor');
        $this->updateTextValue('Category', 'Project', 'Select Category', 'choice-category');
        $this->updateTextValue('About', 'Page', 'About', 'ckeditor');
        $this->updateTextValue('Documents', 'Page', 'About ONEArmenia', 'ckeditor');
        $this->updateTextValue('Interviewers', 'Page', 'About ONEArmenia', 'ckeditor');
       // $this->addListAttribute('Team members', 'Page', 'Member Position', 'choice-members', 'who_we_are');


        exit;
        /**
         * update linst values of collections elements bu first example
         */
        $this->updateSortOrderingListValues('remove-landmines-from-artsakh', true, 'Blocks');
        $this->updateSortOrderingListValues('poxos-posoxsyan', true, 'Countries');
        $this->updateSortOrderingListValues('helen-aivazian-ambassador', true, 'Team members');
        exit;
        $page = $em->getRepository('AppBundle:Page')->findForShow('who_we_are');

        $onjId = $em->getRepository('AppBundle:CollectionValues')->findUniq('Team members', $page->getId(), AttributesDefinition::IS_PAGE);
        $i = 0;
        $j = 0;
        foreach ($onjId->getListValues() as $item){
            if ($item instanceof ConfiguratorInterface)

                foreach ($item->getSettings() as $setting){
                if($setting instanceof AttributabeleInterface && $setting->getAttributesDefinition()->getAttrName() === 'Pamphlet'){
                    $i++;
                    $output->writeln("<info>Remove {$item->getName()} </info>");
                    $em->remove($setting);
                    foreach ($item->getFile() as $fl){
                        $j++;
                        $em->remove($fl);
                    }

                }

            }
        }
        $em->flush();
        $output->writeln("<info>Remove {$i} settings and {$j} Files </info>");


        exit;
        //TODO add category;

        $projects = $em->getRepository('AppBundle:Project')->findAll();

        $attrDefin = new AttributesDefinition();
        $attrDefin->setStatus(true);
        $attrDefin->setIsRequired(true);
        $attrDefin->setIsPublic(true);
        $attrDefin->setAttrName('Category');
        $attrDefin->setAttrClass(AttributesDefinition::IS_LIST);
        $em->persist($attrDefin);

//        $attrDefinList = new AttributesDefinition();
//        $attrDefinList->setStatus(true);
//        $attrDefinList->setIsRequired(true);
//        $attrDefinList->setIsPublic(true);
//        $attrDefinList->setAttrName('Icon');
//        $attrDefinList->setAttrClass(AttributesDefinition::IS_IMAGE);

        $attrDefinListName = new AttributesDefinition();
        $attrDefinListName->setStatus(true);
        $attrDefinListName->setIsRequired(true);
        $attrDefinListName->setIsPublic(true);
        $attrDefinListName->setAttrName('Title');
        $attrDefinListName->setAttrClass(AttributesDefinition::IS_TEXT);
        $em->persist($attrDefinListName);

        $em->flush();

        foreach ($projects as $project){

            $projectSettings = new AttributesProjectSettings();
            $projectSettings->setSortOrdering(count($project->getSettings())+1);
            $projectSettings->setBelongsTo($project);
            $projectSettings->setIsEnable(true);
            $projectSettings->setAttributesDefinition($attrDefin);
            $em->persist($projectSettings);

            $listItem = new ListValues();
            $listItem->setBelongsToObject($project->getId());
            $listItem->setBelongsToObjectName(AttributesDefinition::IS_PROJECT);
            $listItem->setName('Category');
            $listItem->setCollectionValues(null);
            $listItem->setSortOrdering(1);
            $em->persist($listItem);

//            $listSettings = new AttributesListSettings();
//            $listSettings->setSortOrdering(2);
//            $listSettings->setBelongsTo($listItem);
//            $listSettings->setIsEnable(true);
//            $listSettings->setAttributesDefinition($attrDefinList);
//            $em->persist($listSettings);

            $listSettings = new AttributesListSettings();
            $listSettings->setSortOrdering(1);
            $listSettings->setBelongsTo($listItem);
            $listSettings->setIsEnable(true);
            $listSettings->setAttributesDefinition($attrDefinListName);
            $em->persist($listSettings);

//            $icon = new Image();
//            $icon->setSortOrdering(1);
//            $icon->setTitle('Icon');
//            $icon->setBelongsToObject($listItem);
//            $em->persist($icon);

            $text = new TextValues();
            $text->setSortOrdering(1);
            $text->setTitle('Title');
            $text->setBelongsToObject($listItem);
            $em->persist($text);
        }

        $campains = $em->getRepository('AppBundle:Campaign')->findAll();

        $attrDefin = new AttributesDefinition();
        $attrDefin->setStatus(true);
        $attrDefin->setIsRequired(true);
        $attrDefin->setIsPublic(true);
        $attrDefin->setAttrName('Category');
        $attrDefin->setAttrClass(AttributesDefinition::IS_LIST);
        $em->persist($attrDefin);

//        $attrDefinList = new AttributesDefinition();
//        $attrDefinList->setStatus(true);
//        $attrDefinList->setIsRequired(true);
//        $attrDefinList->setIsPublic(true);
//        $attrDefinList->setAttrName('Icon');
//        $attrDefinList->setAttrClass(AttributesDefinition::IS_IMAGE);

        $attrDefinListName = new AttributesDefinition();
        $attrDefinListName->setStatus(true);
        $attrDefinListName->setIsRequired(true);
        $attrDefinListName->setIsPublic(true);
        $attrDefinListName->setAttrName('Title');
        $attrDefinListName->setAttrClass(AttributesDefinition::IS_TEXT);
        $em->persist($attrDefinListName);

        $em->flush();

        foreach ($campains as $campain){

            $projectSettings = new AttributesCampaignSettings();
            $projectSettings->setSortOrdering(count($campain->getSettings())+1);
            $projectSettings->setBelongsTo($campain);
            $projectSettings->setIsEnable(true);
            $projectSettings->setAttributesDefinition($attrDefin);
            $em->persist($projectSettings);

            $listItem = new ListValues();
            $listItem->setBelongsToObject($campain->getId());
            $listItem->setBelongsToObjectName(AttributesDefinition::IS_CAMPAIGN);
            $listItem->setName('Category');
            $listItem->setCollectionValues(null);
            $listItem->setSortOrdering(1);
            $em->persist($listItem);

//            $listSettings = new AttributesListSettings();
//            $listSettings->setSortOrdering(2);
//            $listSettings->setBelongsTo($listItem);
//            $listSettings->setIsEnable(true);
//            $listSettings->setAttributesDefinition($attrDefinList);
//            $em->persist($listSettings);

            $listSettings = new AttributesListSettings();
            $listSettings->setSortOrdering(1);
            $listSettings->setBelongsTo($listItem);
            $listSettings->setIsEnable(true);
            $listSettings->setAttributesDefinition($attrDefinListName);
            $em->persist($listSettings);

//            $icon = new Image();
//            $icon->setSortOrdering(1);
//            $icon->setTitle('Icon');
//            $icon->setBelongsToObject($listItem);
//            $em->persist($icon);

            $text = new TextValues();
            $text->setSortOrdering(1);
            $text->setTitle('Title');
            $text->setBelongsToObject($listItem);
            $em->persist($text);
//            $text->s
        }
        $em->flush();
        exit;
        /**
         * todo: add collection arrtibute
         */
/*        $page = $em->getRepository('AppBundle:Page')->findForShow('oneonone');

        $onjId = $em->getRepository('AppBundle:CollectionValues')->findUniq('Interviewers', $page->getId(), AttributesDefinition::IS_PAGE);

        $attrDefin = new AttributesDefinition();
        $attrDefin->setStatus(true);
        $attrDefin->setIsRequired(true);
        $attrDefin->setIsPublic(true);
        $attrDefin->setAttrName('Active');
        $attrDefin->setAttrClass(AttributesDefinition::IS_BOOL);
        $em->persist($attrDefin);
        $em->flush();

        foreach ($onjId->getListValues() as $listValue){
            $newSettings = new AttributesListSettings();
            $newSettings->setAttributesDefinition($attrDefin);
            $newSettings->setIsEnable(true);
            $newSettings->setSortOrdering(10);
            $newSettings->setBelongsTo($listValue);

            $newAttr = new BooleanValues();
            $newAttr->setSortOrdering(10);
            $newAttr->setBelongsToObject($listValue);
            $newAttr->setTitle('Active');
            $newAttr->setValue(true);
            $em->persist($newAttr);
            $em->persist($newSettings);
        }
$em->flush();
dump($onjId->getListValues()->first()); exit;*/
//        $collectionValues = $em
//            ->createQueryBuilder()
//            ->select('cv, lv, ld, lt, li, lf, settings, attributesDefinition')
//            ->from('AppBundle:CollectionValues', 'cv')
//            ->leftJoin('cv.listValues', 'lv')
//            ->leftJoin('lv.settings', 'settings')
//            ->leftJoin('settings.attributesDefinition', 'attributesDefinition')
//            ->leftJoin('lv.date', 'ld')
//            ->leftJoin('lv.text', 'lt')
//            ->leftJoin('lv.image', 'li')
//            ->leftJoin('lv.file', 'lf')
//
//            ->where('cv.name = :nm')
//            ->andWhere('cv.belongsToObjectName = :blOb')
//            ->orderBy('cv.belongsToObject', 'ASC')
//            ->setParameter('nm', 'Timeline')
//            ->setParameter('blOb', 'Project')
//            ->getQuery()->getResult()
//        ;
//
//        $projects = $em
//            ->createQueryBuilder()
//            ->select('c')
//            ->from('AppBundle:Project', 'c')
//            ->orderBy('c.id', 'DESC')
//            ->getQuery()->getResult()
//        ;
//
//        $collects = [];
//        foreach ($collectionValues as $collectionValue){
//            $collects[$collectionValue->getBelongsToObject()] = $collectionValue->getId();
//        }
//
//        foreach ($projects as $project){
//            $output->writeln("<info>Starting PROJECT {$project->getName()}</info>");
//
//            if(!in_array($project->getId(), $collects)){
//                $newColl = clone $collectionValues[0];
//                $newColl->setBelongsToObject($project->getId());
//                $output->writeln("<info>Starting COLLECTION VALUES {$newColl->getName()}</info>");
//                $em->persist($newColl);
//
//                $lvs = $collectionValues[0]->getListValues();
//                    $output->writeln("<info>Starting LIST VALUES {$lvs->first()->getName()}</info>");
//                    $newLv = clone $lvs->first();
//                    $newLv->setCollectionValues($newColl);
//                    $newLv->setBelongsToObject($project->getId());
//                    $em->persist($newLv);
//            }
//        }
//
//        $em->flush();
//
//
//
//        dump($collects); exit;



//        is_null($type) ? $type = 'Campaign' : '';

//        $campaign = $em
//            ->createQueryBuilder()
//            ->select('c')
//            ->from('AppBundle:Campaign', 'c')
//            ->orderBy('c.id', 'DESC')
////            ->setMaxResults(1)
//            ->getQuery()->getResult()
//        ;
//
//        $arrtDefinsCmp = $em->createQueryBuilder()
//            ->select('adef')
//            ->from('AppBundle:AttributesDefinition', 'adef')
//            ->where('adef.slug LIKE :objName')
//
//            ->setParameter('objName', 'campaign%')
//            ->getQuery()->getResult()
//            ;
//
//        $arrtsCmp = [];
//        $i = 0;
//        foreach ($arrtDefinsCmp as $arrtDefin){
//            if(!in_array($arrtDefin->getAttrName(), $arrtsCmp)){
//                $i++;
//                $arrtsCmp[$arrtDefin->getAttrName()] = $arrtDefin;
//
//                foreach ($campaign as $value){
//                    $campaignSetting = new AttributesCampaignSettings();
//                    $campaignSetting->setSortOrdering($i);
//                    $campaignSetting->setIsEnable(true);
//                    $campaignSetting->setAttributesDefinition($arrtDefin);
//                    $campaignSetting->setBelongsTo($value);
//                    $em->persist($campaignSetting);
//                }
//            }else {
//                $em->remove($arrtDefin);
//            }
//        }

        /**
         * For projects
         */


        /**
         * For list values
         */
       /* $listValues = $em
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:ListValues', 'c')
            ->where('c.belongsToObjectName != :val')
            ->setParameter('val', 'Page')
            ->orderBy('c.id', 'DESC')
            ->getQuery()->getResult()
        ;




        $arrtsList = [];
        $i = 0;
        foreach ($listValues as $listValue){


            if(!$listValue->getImage()->isEmpty()){

                foreach ($listValue->getImage() as $img){

                    $i++;

                    $img->getTitle();
                    $attr = $this->findArrt($img->getTitle(), AttributesDefinition::IS_IMAGE);

                    if($attr){
                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($attr);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }else {
                        $arrtDefin = new AttributesDefinition();
                        $arrtDefin->setAttrName($img->getTitle());
                        $arrtDefin->setAttrClass(AttributesDefinition::IS_IMAGE);
                        $arrtDefin->setIsPublic(true);
                        $arrtDefin->setIsRequired(true);
                        $arrtDefin->setStatus(true);
                        $em->persist($arrtDefin);

                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($arrtDefin);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }
                }
            }

            if(!$listValue->getFile()->isEmpty()){
                foreach ($listValue->getFile() as $img){

                    $i++;

                    $img->getTitle();
                    $attr = $this->findArrt($img->getTitle(), AttributesDefinition::IS_FILE);

                    if($attr){
                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($attr);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }else {
                        $arrtDefin = new AttributesDefinition();
                        $arrtDefin->setAttrName($img->getTitle());
                        $arrtDefin->setAttrClass(AttributesDefinition::IS_FILE);
                        $arrtDefin->setIsPublic(true);
                        $arrtDefin->setIsRequired(true);
                        $arrtDefin->setStatus(true);
                        $em->persist($arrtDefin);

                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($arrtDefin);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }
                }
            }

            if(!$listValue->getText()->isEmpty()){
                foreach ($listValue->getText() as $img){

                    $i++;

                    $img->getTitle();
                    $attr = $this->findArrt($img->getTitle(), AttributesDefinition::IS_TEXT);

                    if($attr){
                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($attr);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }else {
                        $arrtDefin = new AttributesDefinition();
                        $arrtDefin->setAttrName($img->getTitle());
                        $arrtDefin->setAttrClass(AttributesDefinition::IS_TEXT);
                        $arrtDefin->setIsPublic(true);
                        $arrtDefin->setIsRequired(true);
                        $arrtDefin->setStatus(true);
                        $em->persist($arrtDefin);

                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($arrtDefin);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }
                }
            }

            if(!$listValue->getBoolean()->isEmpty()){
                foreach ($listValue->getBoolean() as $img){

                    $i++;

                    $img->getTitle();
                    $attr = $this->findArrt($img->getTitle(), AttributesDefinition::IS_BOOL);

                    if($attr){
                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($attr);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }else {
                        $arrtDefin = new AttributesDefinition();
                        $arrtDefin->setAttrName($img->getTitle());
                        $arrtDefin->setAttrClass(AttributesDefinition::IS_BOOL);
                        $arrtDefin->setIsPublic(true);
                        $arrtDefin->setIsRequired(true);
                        $arrtDefin->setStatus(true);
                        $em->persist($arrtDefin);

                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($arrtDefin);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }
                }
            }

            if(!$listValue->getDate()->isEmpty()){
                foreach ($listValue->getDate() as $img){

                    $i++;

                    $img->getTitle();
                    $attr = $this->findArrt($img->getTitle(), AttributesDefinition::IS_DATE);

                    if($attr){
                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($attr);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }else {
                        $arrtDefin = new AttributesDefinition();
                        $arrtDefin->setAttrName($img->getTitle());
                        $arrtDefin->setAttrClass(AttributesDefinition::IS_DATE);
                        $arrtDefin->setIsPublic(true);
                        $arrtDefin->setIsRequired(true);
                        $arrtDefin->setStatus(true);
                        $em->persist($arrtDefin);

                        $campaignSetting = new AttributesListSettings();
                        $campaignSetting->setSortOrdering($i);
                        $campaignSetting->setIsEnable(true);
                        $campaignSetting->setAttributesDefinition($arrtDefin);
                        $campaignSetting->setBelongsTo($listValue);
                        $em->persist($campaignSetting);
                    }
                }
            }
        }

        $em->flush();*/
    }

    public function findArrt($name, $type){
        $em = $this->getContainer()->get('doctrine')->getManager();

        $arrtDefinsList = $em->createQueryBuilder()
                ->select('adef')
                ->from('AppBundle:AttributesDefinition', 'adef')
                ->where('adef.attrName = :objName')
                ->andWhere('adef.attrClass = :obj')
                ->orderBy('adef.id', 'DESC')
                ->setMaxResults(1)
                ->setParameter('objName', $name)
                ->setParameter('obj', $type)
                ->getQuery()->getOneOrNullResult()
            ;

        return $arrtDefinsList;
    }

    /**
     * @param $exampleSlug
     * @param bool $isCollection
     * @param null $collectionName
     */
    public function updateSortOrderingListValues($exampleSlug, $isCollection = false, $collectionName = null){

        $em = $this->getContainer()->get('doctrine')->getManager();

        if($isCollection == true){

            $listItems = $em->getRepository('AppBundle:CollectionValues')->findOneBy(['name'=>$collectionName]);
        }

        $trueSettings = null;
        $trueList = null;

        foreach ($listItems->getListValues() as $listItem){

            if($listItem->getSlug() == $exampleSlug){
                $trueSettings = $listItem->getSettings();
                $trueList = $listItem;
            }

            if(!is_null($trueSettings) && !is_null($trueList)) {
                foreach ($trueSettings as $trueSetting) {

                    $idsToFilter = array($trueSetting->getAttributesDefinition());

                    $setting = $listItem->getSettings()->filter(

                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getAttributesDefinition(), $idsToFilter);
                        }
                    );

                    if(!$setting->isEmpty()){
                        $setting->first()->setSortOrdering($trueSetting->getSortOrdering());
                        $em->persist($setting->first());
                    }

                    try{

                        $em->getRepository('AppBundle:'.$trueSetting->getAttributesDefinition()->getAttrClass())
                            ->updateOrdering($trueSetting->getAttributesDefinition()->getAttrName(), $trueSetting->getSortOrdering(), $listItem->getId());
                    }catch(\Exception $e){
                        dump($trueSetting->getAttributesDefinition()->getAttrClass());
                        dump($e->getMessage());
//                        $log->addInfo("Request Can`t update attr name : {$e->getMessage()}\r\n");
                    }
                }
            }
        }

        $em->flush();
    }

    /**
     * Use this function for sort
     * @param $objectName
     * @param $objectType
     */
    private function updateAttributesOrdering($objectName, $objectType){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $listValues = $em->getRepository('AppBundle:ListValues')->findBy(['name'=>$objectName, 'belongsToObjectName'=>$objectType]);

        foreach ($listValues as $listValue){

            if(!$listValue->getSettings()->isEmpty()){

                $idsToFilter = array(AttributesDefinition::IS_FILE);

                $setting = $listValue->getSettings()->filter(

                    function($entry) use ($idsToFilter) {
                        return in_array($entry->getAttributesDefinition()->getAttrClass(), $idsToFilter);
                    }
                );

                if(!$setting->isEmpty()){
                    foreach ($setting as $item){
                        $attrCheckName = $item->getAttributesDefinition()->getAttrName();
                        if($attrCheckName === 'Project Sheet'){
                            $sortOredering = -3;
                            $attrName = 'Project Sheet';

                        }elseif ($attrCheckName === 'Budget Sheet'){
                            $sortOredering = -2;
                            $attrName = 'Budget Sheet';

                        }elseif ($attrCheckName === 'Expense Sheet'){
                            $sortOredering = -1;
                            $attrName = 'Expense Sheet';
                        }

                        $sortOredering ? $item->setSortOrdering($sortOredering) : '';

                        if(!$listValue->getFile()->isEmpty() and isset($attrName)){

                            $attrNames = array($attrName);

                            $files = $listValue->getFile()->filter(

                                function($entry) use ($attrNames) {
                                    return in_array($entry->getTitle(), $attrNames);
                                }
                            );

                            if(!$files->isEmpty()){
                                $files->first()->setSortOrdering($sortOredering);
                                $em->persist($files->first());
                            }
                        }
                        $sortOredering ? $em->persist($item) : '';
                    }
                }

                try{

                    $em->flush();
                }catch(\Exception $e){
                    dump($e->getMessage());
//                        $log->addInfo("Request Can`t update attr name : {$e->getMessage()}\r\n");
                }
            }
        }
    }

    /**
     * ToDo use for document. Not documents List
     * @param $objectName
     * @param $objectType
     * @param $title
     * @param $formType
     */
    public function updateTextValue($objectName, $objectType, $title, $formType){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $listValues = $em->getRepository('AppBundle:ListValues')->findBy(['name'=>$objectName, 'belongsToObjectName'=>$objectType]);

        $blObjIds = [];

        foreach ($listValues as $listValue){

            if(!$listValue->getText()->isEmpty()){

                $idsToFilter = array($title);

                $texts = $listValue->getText()->filter(

                    function($entry) use ($idsToFilter) {
                        return in_array($entry->getTitle(), $idsToFilter);
                    }
                );

                if(!$texts->isEmpty()){
                    $texts->first()->setFormType($formType);
                    $em->persist($texts->first());
                }
            }
        }
        $em->flush();
    }

    /**
     * ToDo use for documents List
     * @param $objectName
     * @param $objectType
     * @param $title
     * @param $formType
     */
    public function updateTextValueFormType($objectName, $objectType, $title, $formType) {

        $em = $this->getContainer()->get('doctrine')->getManager();

        $collections = $em->getRepository('AppBundle:CollectionValues')->findBy(['name'=>$objectName, 'belongsToObjectName'=>$objectType]);

        $listValues = [];
        if(!$collections){

            $listValues = $em->getRepository('AppBundle:ListValues')->findBy(['name'=>$objectName, 'belongsToObjectName'=>$objectType]);
        } else {
            foreach ($collections as $collection){
                if(!$collection->getListValues()->isEmpty()){
                    foreach ($collection->getListValues() as $listValue){

                        $listValues[] = $listValue;
                    }
                }
            }
        }
//dump($listValues); exit;
        $blObjIds = [];

        foreach ($listValues as $listValue){

            if(!$listValue->getText()->isEmpty()){

                $idsToFilter = array($title);

                $texts = $listValue->getText()->filter(

                    function($entry) use ($idsToFilter) {
                        return in_array($entry->getTitle(), $idsToFilter);
                    }
                );

                if(!$texts->isEmpty()){
                    $texts->first()->setFormType($formType);
                    $em->persist($texts->first());
                }
            }
        }
        $em->flush();
    }

    private function addListAttribute($collectionName, $objectType, $title, $formType, $slug = null){

        $em = $this->getContainer()->get('doctrine')->getManager();

        if($objectType == AttributesDefinition::IS_PAGE){

            $page = $em->getRepository('AppBundle:Page')->findForShow($slug);

            $onjId = $em->getRepository('AppBundle:CollectionValues')->findUniq($collectionName, $page->getId(), AttributesDefinition::IS_PAGE);
        }

        $attrDefin = new AttributesDefinition();
        $attrDefin->setStatus(true);
        $attrDefin->setIsRequired(true);
        $attrDefin->setIsPublic(true);
        $attrDefin->setAttrName($title);
        $attrDefin->setAttrClass(AttributesDefinition::IS_TEXT);
        $em->persist($attrDefin);
        $em->flush();

        foreach ($onjId->getListValues() as $listValue){

            $newSettings = new AttributesListSettings();
            $newSettings->setAttributesDefinition($attrDefin);
            $newSettings->setIsEnable(true);
            $newSettings->setSortOrdering(10);
            $newSettings->setBelongsTo($listValue);

            $idsToFilter = array(true);

            $bools = $listValue->getBoolean()->filter(

                function($entry) use ($idsToFilter) {
                    return in_array($entry->getValue(), $idsToFilter);
                }
            );
            $categoryes = ['Staff'=>'Staff', 'Ambassador'=>'Ambassador', 'Board Member'=>'Board Member'];
            $value = null;
            foreach ($bools as $bool){
                if(in_array($bool->getTitle(), $categoryes)){
                    $value = $bool->getTitle();

                    $newAttr = new TextValues();
                    $newAttr->setSortOrdering(10);
                    $newAttr->setBelongsToObject($listValue);
                    $newAttr->setTitle($title);
                    $newAttr->setFormType($formType);
                    $newAttr->setValue($value);

                    $settings = $listValue->getSettings();

                    $idsToFilter = ['Staff', 'Ambassador', 'Board Member'];

                    $settingsRm = $settings->filter(


                    function($entry) use ($idsToFilter) {
                                return in_array($entry->getAttributesDefinition()->getAttrName(), $idsToFilter);
                            }
                        );

                    foreach ($settingsRm as $setingRm){

                        $em->remove($setingRm);
                    }

                    $bools = $listValue->getBoolean()->filter(

                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getTitle(), $idsToFilter);
                        }
                    );
                    foreach ($bools as $boolRm){

                        $em->remove($boolRm);
                    }
                    $em->persist($newAttr);
                    $em->persist($newSettings);
                }
            }
        }

        $em->flush();
    }

    public function removeAttribute($objectType, $parentClass, $parentObjectName, $removedName){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $objects = $em->getRepository('AppBundle:ListValues')->findBy(['belongsToObjectName'=>$objectType,
                                                                                    'name'=>$parentObjectName]);

        if($objects){
            foreach ($objects as $object){

                if($object instanceof ListValues){

                    $settings = $object->getSettings();

                    $idsToFilter = [$removedName];

                    $settingsRm = $settings->filter(


                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getAttributesDefinition()->getAttrName(), $idsToFilter);
                        }
                    );

                    foreach ($settingsRm as $setingRm){

                        $em->remove($setingRm);
                    }

                    $textes = $object->getText()->filter(

                        function($entry) use ($idsToFilter) {
                            return in_array($entry->getTitle(), $idsToFilter);
                        }
                    );

                    foreach ($textes as $texte){

                        $em->remove($texte);
                    }
                }
            }
        }

        $em->flush();
    }

    /**
     * todo: project sorting
     */
    private function checkProjectOrdering($output){
        $em = $this->getContainer()->get('doctrine')->getManager();

        $dates = $em ->getRepository('AppBundle:DateValues')->findByOrderDate('Start Date');
//        dump()
        $cnt = count($dates);
        $ides = [];
        foreach ($dates as $date){
            if(!is_null($date->getBelongsToObject())){
                $output->writeln("<info>Update {$date->getTitle()} </info>");
                $t = $date->getBelongsToObject()->getBelongsToObject();
                $ides[] = $t;
                $project = $em->getRepository('AppBundle:Project')->find((int)$t);
                if($project){

                    $project->setSortOrderDate($date->getValue());
                    $em->persist($project);
                }
            }
        }
        $em->flush();
        $output->writeln("<info>Finish {$cnt} item </info>");
    }
}
