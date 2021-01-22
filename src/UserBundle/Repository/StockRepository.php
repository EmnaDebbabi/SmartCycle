<?php
namespace UserBundle\Repository;
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 3/30/2019
 * Time: 1:47 PM
 */
use Doctrine\ORM\EntityRepository;


class StockRepository extends EntityRepository
{

    public function findByDescriptionAction($description )
    {
        $query=getEntryManager()
            ->createQuery("select s
           from  UserBundle:Stock s WHERE s.description=:description
           ")
            -> setParameter('description',$description);
        return $query->getResult();
    }
    public function findEntitiesByString($str)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT s
                FROM UserBundle:Stock s
                WHERE s.description LIKE :str'
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();

    }
}