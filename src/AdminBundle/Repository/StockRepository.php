<?php


namespace AdminBundle\Repository;
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 3/29/2019
 * Time: 3:49 PM
 */

use Doctrine\ORM\EntityRepository;

class StockRepository extends EntityRepository
{
    public function findOneBydescriptionAction($description )
    {
        $query=getEntryManager()
            ->createQuery("select s
           from  UserBundle:Stock s WHERE s.description=:description
           ")
            -> setParameter('description',$description);
        return $query->getResult();
    }

}