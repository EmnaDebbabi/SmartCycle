<?php
/**
 * Created by PhpStorm.
 * User: jalel
 * Date: 03/04/2019
 * Time: 14:59
 */

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Reply;
use UserBundle\Entity\Topic;
use UserBundle\Form\ReplyType;
use UserBundle\Form\TopicType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use UserBundle\UserBundle;

class ForumController extends Controller
{
    public function addAction(Request $request)
    {
        //On recupere l'EntityManager

        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        ///Pour recuperer les entrees de la form comme post dans le
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            // $topic->setTopicCat($this->getCa);
            $topic->__construct();
            $topic->setEtat(0);
            $topic->setRate(0);
            $topic->setLikes(0);

            $topic->setDispo(0);
            $topic->uploadProfilePicture();
            $topic->setTopicBy($this->getUser());

            //On persiste l'entite
            $em->persist($topic);
            ///for the execution
            //On flush ce qui a ete persiste avant
            $em->flush();
            ///for showing redirection
            return $this->redirectToRoute("listTopic");
            //hedhy ki yajouty yraja3ni lel affichage

        }
        return $this->render("@User/Forum/addTopic.html.twig", array(
            'form' => $form->createView()
        ));
    }


    public function showTopicAction(topic $id, Request $request)
    {


        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $reply = new Reply();
        $offre = $em->getRepository('UserBundle:Topic')->find($id);
        $form = $this->createForm('UserBundle\Form\ReplyType', $reply);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setDateCreation(new \DateTime());
            $reply->setLikes(0);
            $reply->setSujetId($offre->getTopicId());
            $reply->setUser($user);

            $offre->addCommentaire($reply);
            $em = $this->getDoctrine()->getManager();
            $em->persist($reply);
            $em->flush();
        }

        $topic = $em->getRepository("UserBundle:Topic")->findReply();


        return $this->render('@User/Forum/showTopic', array(
            'event' => $offre,
            'form' => $form->createView(),
            'user' => $user,
            'reply' => $reply
        ));


    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $topic = $em->getRepository("UserBundle:Topic")->listBy();

        return $this->render("@User/Forum/listerTopic.html.twig", array(
            'topics' => $topic
        ));
    }
    //////search ajax
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository('UserBundle:Topic')->findEntitiesByStrings($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Evénement Not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getTopicId()] = [$posts->getPhoto(),$posts->getTopicTitle()];
        }
        return $realEntities;
    }
/////////
    public function updateTopicAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $topic = $em->getRepository("UserBundle:Topic")
            ->find($id);
        /*$img=$topic->getPhoto();*/

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            //$em->persist($topic);
            $em->flush();
            return $this->redirectToRoute("listTopic");

        }
        return $this->render("@User/Forum/updateTopic", array(
            //'topics' => $topic,
            'form' => $form->createView()
            //,'img'=>$img
        ));

    }

    public function updateCommentaireAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $topic = $em->getRepository("UserBundle:Reply")
            ->find($id);
        /*$img=$topic->getPhoto();*/

        $form = $this->createForm(ReplyType::class, $topic);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            //$em->persist($topic);
            $em->flush();
            return $this->redirectToRoute("listTopic");

        }
        return $this->render("@User/Forum/updateCommentaire", array(
            //'topics' => $topic,
            'form' => $form->createView()
            //,'img'=>$img
        ));

    }


    public function deleteTopicAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('UserBundle:Topic')->find($id);
        $em->remove($cat);
        $em->flush();
        /*return $this->render("@Admin/Forum/listeCategorie",array(
            'Categorietopics'=>$cat
        ));*/
        return $this->redirectToRoute("listTopic");

    }


    public function deleteCommentaireAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('UserBundle:Reply')->find($id);
        $em->remove($cat);
        $em->flush();
        /*return $this->render("@Admin/Forum/listeCategorie.html.twig",array(
            'Categorietopics'=>$cat
        ));*/
        return $this->redirectToRoute("listTopic");

    }


    public function signalerAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Topic")->find($id);
        $topic->setEtat($topic->getEtat() - 1);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }


    public function signalerCommentaireAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Reply")->find($id);
        $topic->setLikes(-1);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }


    public function oneStarAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Topic")->find($id);
        $topic->setRate(0);
        $topic->setRate($topic->getRate() + 1);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }

    public function twoStarAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Topic")->find($id);
        $topic->setRate(0);
        $topic->setRate($topic->getRate() + 2);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }

    public function threeStarAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Topic")->find($id);
        $topic->setRate(0);
        $topic->setRate($topic->getRate() + 3);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }

    public function fourStarAction(Request $request)
    {

        $msg = ' Cliquez pour signaler!';
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $topic = new Topic();
        $topic = $em->getRepository("UserBundle:Topic")->find($id);
        $topic->setRate(0);
        $topic->setRate($topic->getRate() + 4);
        $em->persist($topic);
        $em->flush();

        return $this->redirectToRoute("listTopic");

    }

//
//    public function RechercheAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $requestString = $request->get('search');
//        $topic = $em->getRepository('UserBundle:Topic')->findEntitiesByString($requestString);
//        if (!$topic) {
//            $result['topics']['error'] = "Post Not found :( ";
//        } else {
//            $result['topics'] = $this->getRealEntities($topic);
//        }
//        return new Response(json_encode($result));
//    }

//    public function getRealEntities($topic)
//    {
//        foreach ($topic as $topic) {
//            $realEntities[$topic->getTopicId()] = [$topic->getPhoto(), $topic->getTopicTitle()];
//        }
//        return $realEntities;
//
//
//    }
}