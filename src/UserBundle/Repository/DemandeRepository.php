<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 14/04/2019
 * Time: 20:50
 */

namespace UserBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use UserBundle\Entity\Demande;

class DemandeRepository extends EntityRepository
{
    public function findAdherationStats(){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT d  ,UPPER(a.nomAssociation) as nom ,COUNT(d.idm) as num FROM UserBundle:Demande d 
join UserBundle:Association a with a.idAssociation=d.ida WHERE d.etat = 'valider' GROUP BY d.ida"
            )
            ->getResult();
    }


}