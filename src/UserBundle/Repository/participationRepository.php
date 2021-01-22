<?php

/**
 * Created by PhpStorm.
 * User: debba
 * Date: 3/29/2019
 * Time: 7:07 PM
 */
namespace UserBundle\Repository;
class participationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByParticipationDQL($iduser)
    {
        $Query=$this->getEntityManager()
            ->createQuery("select e  from UserBundle:Evttesting JOIN UserBundle:Participant p where p.idm =:iduser ")
//            ->createQuery(" select v from ClientBundle:Participation v where v.idevent like :idevent  ")
            ->setParameter('idusert',$iduser);


        return $Query->getResult();

    }
    public function countParticipantParEvent()
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT idevent ,COUNT(idp) FROM UserBundle:Participant GROUP by idevent");

        return $query->getResult();

    }

}