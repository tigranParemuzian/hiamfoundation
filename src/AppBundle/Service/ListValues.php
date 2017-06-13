<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/22/17
 * Time: 6:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\BooleanValues;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\DateValues;
use AppBundle\Entity\File;
use AppBundle\Entity\Image;
use AppBundle\Entity\TextValues;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Entity\ListValues as ListValuesEntity;

class ListValues
{


    /**
     * Symfony\Component\DependencyInjection\Container
     *
     * @var Container
     */
    private $container;
    private $settigs;
    private $em;

    const IS_EDIT = 0;
    const IS_CREATE = 1;
    const IS_DELETE = 2;

    public function __construct(Container $container, $settigs = [])
    {
        $this->container = $container;
        $this->settigs = $settigs;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function checkAttributes($object, $settings){

        $this->em = $this->container->get('doctrine')->getManager();

        $realSettings = [];

        $uow = $this->em->getUnitOfWork();

        if($object->getSettings()){

            $i = 0;
            foreach ($object->getSettings() as $item){

                $item = $item->getAttributesDefinition();
                $realSettings[] = ['types'=>$item->getAttrClass(), 'name'=>$item->getAttrName()];
                $i++;
            }
        }
    }

    /**
     * @param $object
     * @param $realSettings
     */
    public function createAttributes($object, $realSettings){


        foreach ($realSettings as $realSetting){

            switch ($realSetting['types']) {

                case AttributesDefinition::IS_IMAGE:
                    $object->getImage();

                    if($object->getImage()->isEmpty()){

                        $this->crudImage($object, $realSetting, self::IS_CREATE);

                    }else{

                        $idsToFilter = array($realSetting['name']);

                        $imgByName = $object->getImage()->filter(

                            function($entry) use ($idsToFilter) {
                                return in_array($entry->getTitle(), $idsToFilter);
                            }
                        );
                        if(count($imgByName) == 0){
                            $this->crudImage($object, $realSetting, self::IS_CREATE);
                        }else{
                            $this->crudImage($object, $realSetting, self::IS_EDIT, $imgByName);
                        }
                    }
                    break;

                case AttributesDefinition::IS_FILE:

                    if($object->getFile()->isEmpty()){

                       $this->crudFile($object, $realSetting, self::IS_CREATE);

                    }else {

                        $idsToFilter = array($realSetting['name']);

                        $fileByName = $object->getFile()->filter(

                            function($entry) use ($idsToFilter) {
                                return in_array($entry->getTitle(), $idsToFilter);
                            }
                        );

                        if(count($fileByName) == 0){
                            $this->crudFile($object, $realSetting, self::IS_CREATE);
                        }else{
                            $this->crudImage($object, $realSetting, self::IS_EDIT, $fileByName);
                        }
                    }
                    break;

                case AttributesDefinition::IS_TEXT:
                    if($object->getText()->isEmpty()){
                        $this->crudText($object, $realSetting, self::IS_CREATE);
                    }else {
                        $idsToFilter = array($realSetting['name']);

                        $textByName = $object->getText()->filter(
                            function($entry) use ($idsToFilter) {
                                return in_array($entry->getTitle(), $idsToFilter);
                            }
                        );

                        if(count($textByName) == 0){
                            $this->crudText($object, $realSetting, self::IS_CREATE);
                        }
                    }
                    break;

                case AttributesDefinition::IS_DATE:

                    if($object->getDate()->isEmpty()){

                        $this->crudDate($object, $realSetting, self::IS_CREATE);
                    }else {

                        $idsToFilter = array($realSetting['name']);

                        $textByName = $object->getDate()->filter(
                            function($entry) use ($idsToFilter) {
                                return in_array($entry->getTitle(), $idsToFilter);
                            }
                        );

                        if(count($textByName) == 0){
                            $this->crudDate($object, $realSetting, self::IS_CREATE);
                        }
                    }
                    break;
                    case AttributesDefinition::IS_BOOL:

                    if($object->getBoolean()->isEmpty()){

                        $this->crudBolean($object, $realSetting, self::IS_CREATE);
                    }else {

                        $idsToFilter = array($realSetting['name']);

                        $textByName = $object->getBoolean()->filter(
                            function($entry) use ($idsToFilter) {
                                return in_array($entry->getTitle(), $idsToFilter);
                            }
                        );

                        if(count($textByName) == 0){
                            $this->crudBolean($object, $realSetting, self::IS_CREATE);
                        }
                    }
                    break;

                default:
                    break;
            }
        }
    }



    public function createDocument($object){

        switch ($object->getAttrClass()){
            case AttributesDefinition::IS_LIST:
                $this->createListValues($object);
                break;
            case AttributesDefinition::IS_COLLECTION;
                $collection = $this->createCollection($object, []);
                $this->createListValues($object, $collection);
                break;
            default;
            break;
        }
    }

    private function createListValues($object, $collection =null){

        $uow = $this->em->getUnitOfWork();

        $updates = $uow->getScheduledEntityUpdates();
        $insertions = $uow->getScheduledEntityInsertions();

        $listItem = new ListValuesEntity();
        $listItem->setBelongsToObject((int)$object->getBelongsToObject());
        $listItem->setBelongsToObjectName($object->getObjectName());
        $listItem->setLabel($object->getAttrName());
        $collection ? $listItem->setCollectionValues($collection) : '';
        $metaSubitemColor = $this->em->getClassMetadata(get_class($listItem));

        $uow->computeChangeSet($metaSubitemColor, $listItem);
        $this->em->persist($listItem);
    }

    private function createCollection($object){

        $em = $this->em;
        $uow = $em->getUnitOfWork();

        $collection = new CollectionValues();
        $collection->setBelongsToObject($object->getBelongsToObject());
        $collection->setBelongsToObjectName($object->getObjectName());
        $collection->setLabel($object->getAttrName());

        $logMetadata = $em->getClassMetadata('AppBundle:CollectionValues');



        $em->persist($collection);
        $uow->computeChangeSet($logMetadata, $collection);
        
        return $collection;
    }

    /**
     * This function use to create Image
     *
     * @param $object
     * @param $realSetting
     *
     */
    private function crudImage($object, $realSetting, $state, $images = null){

        switch ($state){
            case self::IS_CREATE:
                $image = new Image();
                $image->setBelongsToObject($object);
                $image->setTitle($realSetting['name']);
                $this->em->persist($image);
                break;
            case self::IS_EDIT:
                foreach ($images as $image){
                    $image->uploadFile();
                    $this->em->persist($image);
                }

                break;
            default:
                break;

        }
    }

    private function crudFile($object, $realSetting, $state, $files = null){
        switch ($state){
            case self::IS_CREATE:
                $file = new File();
                $file->setBelongsToObject($object);
                $file->setTitle($realSetting['name']);
                $this->em->persist($file);
            break;
            case self::IS_EDIT:
                foreach ($files as $file) {
                    $file->uploadFile();
                    $this->em->persist($file);
                }
                break;
            default:
                break;
        }

    }

    private function crudText($object, $realSetting, $state){

        if($state == self::IS_CREATE){

            $text = new TextValues();
            $text->setBelongsToObject($object);
            $text->setTitle($realSetting['name']);
            $text->setValue($realSetting['name']);
            $this->em->persist($text);
        }

    }

    private function crudBolean($object, $realSetting, $state){

        if($state == self::IS_CREATE){

            $text = new BooleanValues();
            $text->setBelongsToObject($object);
            $text->setTitle($realSetting['name']);
            $text->setValue(true);
            $this->em->persist($text);
        }

    }

    private function crudDate($object, $realSetting, $state){

        if($state == self::IS_CREATE){

            $date = new DateValues();
            $date->setBelongsToObject($object);
            $date->setTitle($realSetting['name']);
            $date->setValue(new \DateTime('now'));
            $this->em->persist($date);
        }

    }

}