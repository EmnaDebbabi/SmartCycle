<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 03/04/2019
 * Time: 15:10
 */

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use UserBundle\Entity\Association;


class AssociationRepository extends EntityRepository
{
    public function findEntitiesByString($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM UserBundle:Association a WHERE a.nomAssociation LIKE :str'
            )
            ->setParameter('str','%'.$str.'%')
            ->getResult();

    }

    public function findAcceptedAdherations($idu){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM UserBundle:Association a JOIN UserBundle:Demande d WITH a.idAssociation = d.ida
                      WHERE d.etat = :etat AND d.idm = :idu'
            )
            ->setParameters(array('etat'=>"valider",'idu'=>$idu))
            ->getResult();
    }



}