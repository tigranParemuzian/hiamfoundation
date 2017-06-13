<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/16/17
 * Time: 6:57 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\CampaignAttributeValues;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\DateValues;
use AppBundle\Entity\File;
use AppBundle\Entity\Image;
use AppBundle\Entity\ListValues;
use AppBundle\Entity\Page;
use AppBundle\Entity\Project;
use AppBundle\Entity\TextValues;
use AppBundle\Repository\PageAttributeValuesRepository;
use Symfony\Component\DependencyInjection\Container;

class CalculateValues
{

    const IS_CRETE = 0;
    const IS_EDIT = 1;
    const IS_DELETE = 2;

    /**
     * Symfony\Component\DependencyInjection\Container
     *
     * @var Container
     */
    private $container;
    private $settigs;

    public function __construct(Container $container, $settigs = [])
    {
        $this->container = $container;
        $this->settigs = $settigs;
    }

    public function calculateAttributesDefinitions($object, $settigs){

        $this->settigs = $settigs;
        $this->checkAttributesDefinition($object, $settigs);
    }

    /**
     * This function use to CRUD AttributesDefinition
     *
     * @param $object
     * @param $settigs
     */
    private function checkAttributesDefinition($object, $settigs){

        $em = $this->container->get('doctrine')->getManager();

        $realSettings = [];

        if($object->getAttributesDefinition()){

            if(count($settigs)>1){
                foreach ($settigs as $settig) {
                    if(!in_array($settig, $object->getAttributesDefinition())){
                        $em->remove($settig);
                    }
                }
            }
            $i = 0;
            foreach ($object->getAttributesDefinition() as $item){

                /**
                 * Create Attributes Definition data
                 */
                if(strpos($object->getClassName(), AttributesDefinition::IS_CAMPAIGN) !==false) {
                    $ObjectName = AttributesDefinition::IS_CAMPAIGN;
                }elseif (strpos($object->getClassName(), AttributesDefinition::IS_LIST) !==false){
                    $ObjectName = AttributesDefinition::IS_LIST;
                }elseif (strpos($object->getClassName(), AttributesDefinition::IS_PAGE) !==false){
                    $ObjectName = AttributesDefinition::IS_PAGE;
                }elseif (strpos($object->getClassName(), AttributesDefinition::IS_PROJECT) !==false){
                    $ObjectName = AttributesDefinition::IS_PROJECT;
                }

                $item->setObjectName($ObjectName);
                $item->setModeratorSortOrder($i);
                $item->setBelongsToObject($object->getId());

                $em->persist($item);
                $realSettings[] = ['types'=>$item->getAttrClass(), 'name'=>$item->getAttrName()];
                $i++;
            }
        }else {
            foreach ($settigs as $settig){
                $em->remove($settig);
            }
        }

        if(!is_null($object->getId())){
                $this->checkCampaignAttrValues($object, $realSettings);
            }
    }

    /**
     * Tihs function use to CRUD CampaignAttrValues
     */
    private function checkCampaignAttrValues($object, $settings){

        $em = $this->container->get('doctrine')->getManager();

        if($object instanceof Campaign){
            $belongsToObjectName = AttributesDefinition::IS_CAMPAIGN;
        }elseif ($object instanceof Project){
            $belongsToObjectName = AttributesDefinition::IS_PROJECT;
        }

        // get all Listing values by Campaign
        $listItems = $em->getRepository('AppBundle:ListValues')->findForThisCmp($belongsToObjectName, $object->getId());
        $collectionItems = $em->getRepository('AppBundle:CollectionValues')->findForThisCmpAll($belongsToObjectName, $object->getId());

        $listItemsLabels = [];

        foreach ($listItems as $listItem){

            if($listItem->getLabel()){
                $listItemsLabels[] = $listItem->getLabel();
            }
        }

        foreach ($collectionItems as $listItem){

            if($listItem->getLabel()){
                $listItemsLabels[] = $listItem->getLabel();
            }
        }

        $i = 0;
        foreach ($settings as $setting){

            if(!in_array($setting['name'], $listItemsLabels)){

                switch ($setting['types']){

                    case AttributesDefinition::IS_LIST:
                        $listItem = new ListValues();
                        $listItem->setBelongsToObject($object->getId());
                        $listItem->setBelongsToObjectName($belongsToObjectName);
                        $listItem->setLabel($setting['name']);
                        $em->persist($listItem);
                        break;
                    case AttributesDefinition::IS_COLLECTION:

                        $collection = new CollectionValues();
                        $collection->setBelongsToObject($object->getId());
                        $collection->setBelongsToObjectName($belongsToObjectName);
                        $collection->setLabel($setting['name']);

                        $listItem = new ListValues();
                        $listItem->setBelongsToObject($object->getId());
                        $listItem->setBelongsToObjectName(AttributesDefinition::IS_COLLECTION);
                        $listItem->setCollectionValues($collection);
                        $listItem->setLabel($setting['name'].'_'.$i);

                        $em->persist($collection);
                        $em->persist($listItem);

                        break;
                    default:
                        break;
                }
            }

            $i++;
        }
    }
}