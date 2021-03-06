<?php

namespace AppBundle\Repository;

/**
 * AttributesCampaignSettingsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttributesCampaignSettingsRepository extends \Doctrine\ORM\EntityRepository
{
    public function updateStatus($id, $status){

        $this->getEntityManager()
            ->createQueryBuilder()
            ->update('AppBundle:AttributesCampaignSettings', 'ad')
            ->set('ad.isEnable', ':st')
            ->where('ad.id = :id')
            ->setParameter('id', $id)
            ->setParameter('st', $status)
            ->getQuery()->execute()
        ;
    }

    public function updateOrdering($name){

        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('ad, attr')
            ->from('AppBundle:AttributesCampaignSettings', 'ad')
            ->leftJoin('ad.attributesDefinition', 'attr')
            ->where('attr.attrName = :nm')
            ->setParameter('nm', $name)
            ->getQuery()->getResult()
            ;
    }
}
