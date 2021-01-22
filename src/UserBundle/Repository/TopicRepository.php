<?php
/**
 * Created by PhpStorm.
 * User: jalel
 * Date: 06/04/2019
 * Time: 14:11
 */

namespace UserBundle\Repository;


class TopicRepository extends \Doctrine\ORM\EntityRepository
{
    public function listBy()
    {
        /* $query = $this->getEntityManager();

         //On recupere l'EntityManager
         $em=$this->getDoctrine()->getManager();
         $query = $em->createQuery(" SELECT t FROM UserBundle:Topic t WHERE t.dispo=1 AND t.etat=1");
         return $query->getResult();
     }*/


        $query = $this->getEntityManager()
            ->createQuery("select ev FROM UserBundle:Topic ev  where ev.etat>1 AND ev.dispo=1 ORDER BY ev.rate DESC ");
        // $query = $em->createQuery("select ev FROM UserBundle:Evttesting ev where ev not in ( SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id )");

        $topic = $query->getResult();
        return $topic;

    }


    public function findReply()
    {


        $query = $this->getEntityManager()
            ->createQuery("select ev FROM UserBundle:Reply ev  where ev.id in ( SELECT e.id FROM UserBundle:Reply e JOIN UserBundle:Topic p WHERE p.topicId = e )");
        // $query = $em->createQuery("select ev FROM UserBundle:Evttesting ev where ev not in ( SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id )");

        $events = $query->getResult();
        return $events;

    }

    public function findEntitiesByStrings($str){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM UserBundle:Topic a WHERE a.topicTitle LIKE :str'
            )
            ->setParameter('str','%'.$str.'%')
            ->getResult();

    }
    public function findByTopicTitle($TopicTitle)
    {

        $query = $this->getEntityManager()
            ->createQuery("select ev FROM UserBundle:Topic ev where ev.etat>=1 AND ev.dispo=1 AND ev.topicTitle like :x ORDER BY ev.rate DESC ")
            ->setParameter('x', '%'.$TopicTitle.'%');
        $topic = $query->getResult();
        return $topic;

    }
    public function findByCatTitle($catName)
    {

        $query = $this->getEntityManager()
            ->createQuery("select ev FROM UserBundle:Categorietopics ev where ev.catName like :x")
            ->setParameter('x', '%'.$catName.'%');
        $topic = $query->getResult();
        return $topic;

    }


}