<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use UserBundle\Entity\Rating;
use UserBundle\Entity\Ratinge;


class RatingRepository extends EntityRepository
{
    public function getAssociationsRatings($ida){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT r , ROUND(AVG(r.value)) AS val FROM UserBundle:Rating r WHERE r.ida = :ida'
            )
            ->setParameter('ida',$ida)
            ->getResult();
    }

    public function getEventsRatings($ide){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT r , ROUND(AVG(r.value)) AS val FROM UserBundle:Ratinge r WHERE r.ide = :ide'
            )
            ->setParameter('ide',$ide)
            ->getResult();
    }
}
