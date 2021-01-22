<?php

/**
 * Created by PhpStorm.
 * User: debba
 * Date: 3/29/2019
 * Time: 7:06 PM
 */
namespace UserBundle\Repository;
class EventsRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByTrierEventsUser($id)
    {


        $query = $this->getEntityManager()
            ->createQuery("select ev FROM UserBundle:Evttesting ev  where ev.id not in ( SELECT e.id FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm =:id AND e.idm !=:id ) AND ev.idm!=:id AND ev.date > CURRENT_DATE () ORDER BY ev.date");
        // $query = $em->createQuery("select ev FROM UserBundle:Evttesting ev where ev not in ( SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id )");
        $query->setParameter('id',$id);

       $events = $query->getResult();
       return $events;

    }
    public function findByTrierAnnulerParticipationUser($etat,$id)
    {


        $query = $this->getEntityManager()
            ->createQuery("SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm =:id AND e.idm !=:id AND p.etat like :etat ");
        // $query = $em->createQuery("select ev FROM UserBundle:Evttesting ev where ev not in ( SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id )");
        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();

    }
    //////////////
    public function findEntitiesByString($str)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p
                FROM UserBundle:Evttesting p
                WHERE p.nom LIKE :str '
            )
            ->setParameter('str', '%'.$str.'%')
            ->getResult();

    }
    //////
public function findByTrierEventsParNomAdmin($id){


$query = $this->getEntityManager()
->createQuery("select ev  FROM UserBundle:Evttesting ev  GROUP BY ev.nom");
    // $query = $em->createQuery("select ev FROM UserBundle:Evttesting ev where ev not in ( SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id )");
$events = $query->getResult();
return $events;

}


}
