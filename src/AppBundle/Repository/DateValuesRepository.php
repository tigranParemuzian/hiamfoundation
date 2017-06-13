<?php

namespace AppBundle\Repository;
use AppBundle\Entity\AttributesDefinition;
use AppBundle\Form\AttributesDefinitionType;

/**
 * TextValuesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DateValuesRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * This function use to get by belobg to object
     */
    public function findByObject($obgId, $title = null){

        $q = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('i')
            ->from('AppBundle:DateValues', 'i')
            ->where('i.belongsToObjectId = :onjId');

        if (!is_null($title)){
            $q->
            andWhere('i.title = :it')
                ->setParameter('onjId', $obgId)
                ->setParameter('it', $title)
                ->getQuery()->getOneOrNullResult()
            ;
        }else {
            $q    ->setParameter('onjId', $obgId)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $label
     * @param $objectName
     * @param $belongsToObject
     */
    public function updateByAttributesDefinition($oldLabel, $label, $objectName){

        $this->getEntityManager()
            ->createQueryBuilder()
            ->update('AppBundle:DateValues', 'cv')
            ->set('cv.title', ':lb')
            ->where('cv.title = :oldLb')
//            ->andWhere('cv.belongsToObject = :objid')
            ->setParameter('lb', $label)
            ->setParameter('oldLb', $oldLabel)
//            ->setParameter('objid', $belongsToObject)
            ->getQuery()->execute()
        ;
    }

    /**
     * Update sort order
     *
     * @param $attrId
     * @param $sortOrdering
     * @param $oldSortOrdering
     */
    public function updateOrdering($oldSortOrdering, $sortOrdering, $attrId){

        $this->getEntityManager()
            ->createQueryBuilder()
            ->update('AppBundle:DateValues', 'cv')
            ->set('cv.sortOrdering', ':lb')
            ->where('cv.title = :oldLb')
            ->andWhere('cv.belongsToObject = :objid')
            ->setParameter('lb', $sortOrdering)
            ->setParameter('oldLb', $oldSortOrdering)
            ->setParameter('objid', $attrId)
            ->getQuery()->execute()
        ;
    }

    /**
     * This function use to get data by started date ordering
     *
     * @param $title
     * @return array
     */
    public function findByOrderDate($title){

        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('d, lv')
            ->from('AppBundle:DateValues', 'd')
            ->leftJoin('d.belongsToObject', 'lv')
            ->leftJoin('lv.collectionValues', 'cl')
            ->where('d.title = :tl')
            ->andWhere('lv.belongsToObjectName = :type')
            ->andWhere('lv.name = :nm')
            ->orderBy('d.value','DESC')
            ->setParameter('tl', $title)
            ->setParameter('type', AttributesDefinition::IS_PROJECT)
            ->setParameter('nm:', 'About')
            ->getQuery()->getResult();
            ;
    }
}
