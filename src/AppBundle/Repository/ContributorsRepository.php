<?php

namespace AppBundle\Repository;

/**
 * ContributorsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContributorsRepository extends \Doctrine\ORM\EntityRepository
{

    public function findForViuew($projectId, $limit, $offset)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Contributors', 'c')
            ->where('c.projectId = :pId')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('pId', $projectId)
            ->getQuery()
            ->getResult()
            ;


    }
}
