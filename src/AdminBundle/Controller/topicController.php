<?php
/**
 * Created by PhpStorm.
 * User: jalel
 * Date: 10/04/2019
 * Time: 18:02
 */

namespace AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Topic;
use UserBundle\Form\TopicType;
use Symfony\Component\HttpFoundation\Request;


class topicController extends Controller
{
    public function listTopicAction()
    {
        $em=$this->getDoctrine()->getManager();
        $cat=$em->getRepository("UserBundle:Topic")->findAll();
        return $this->render("@Admin/Forum/listeTopic.html.twig",array(
            'topics'=>$cat
        ));
    }

    public function approuverAction(Request $request)
    {

        $msg  =  'Déjà participant! Cliquez pour annuler!';
        $id=$request->get('topicId');
        $em=$this->getDoctrine()->getManager();
        $topic= new Topic();
        $topic=$em->getRepository("UserBundle:Topic")->find($id);
        $topic->setEtat(5);
        $topic->setDispo(1);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }
    public function refuserAction(Request $request  )
    {
        $id=$request->get('topicId');
        $em= $this->getDoctrine()->getManager();
        $cat=$em->getRepository('UserBundle:Topic')->find($id);
        $em->remove($cat);
        $em->flush();
        /*return $this->render("@Admin/Forum/listeCategorie.html.twig",array(
            'Categorietopics'=>$cat
        ));*/
        return $this->redirectToRoute("listerTopic");

    }

}