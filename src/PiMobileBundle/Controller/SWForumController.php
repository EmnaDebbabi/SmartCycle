<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 5/22/2019
 * Time: 2:19 PM
 */

namespace PiMobileBundle\Controller;


use JsonSchema\Constraints\ObjectConstraint;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\Categorietopics;
use UserBundle\Entity\Reply;
use UserBundle\Entity\Topic;
use UserBundle\Repository\TopicRepository;
use UserBundle\Form\TopicType;

class SWForumController extends Controller
{
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //  $user = $em->getRepository(User::class)->find($request->get('iduser'));
        $topic = new Topic();

        if ($request->isMethod('POST')) {
            // $time = date("Y/m/d");

            $topic->__construct();
            $topic->setEtat(0);
            $topic->setRate(0);
            $topic->setLikes(0);
            $topic->setTopicTitle($request->get('topicTitle'));
            $topic->setTopicSubject($request->get('topicSubject'));
            //$topic->setTopicCat();
            $topic->setDispo(0);
            //$topic->uploadProfilePicture();
            $topic->setPhoto('a3;,:ùù.jpg');
            $topic->setTopicBy($this->getUser());



            $em = $this->getDoctrine()->getManager();
            $em->persist($topic);
            $em->flush();

            return new Response("OK",200);
        }
        return new Response("erreur",500);
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

        $topic = $em->getRepository("UserBundle:Topic")->commentaire($offre->getTopicId());


        return $this->render('@User/Forum/showTopic.html.twig   ', array(
            'event' => $offre,
            'form' => $form->createView(),
            'user' => $user,
            'commentaire' => $topic,
            'reply' => $reply
        ));


    }

    public function listeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($request->get("wi"))
            $topic = $em->getRepository("UserBundle:Topic")->findByTopicTitle($request->get("wi"));
        else
            $topic = $em->getRepository("UserBundle:Topic")->listBy();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($topic);
        return new JsonResponse($formatted);
    }


    public function CatnameAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request->get("wi");
        $topic = $em->getRepository("UserBundle:Topic")->findByCatTitle($request->get("wi"));


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($topic);
        return new JsonResponse($formatted);
    }
    public function listCatAction()
    {
        $em=$this->getDoctrine()->getManager();
        $cat=$em->getRepository("UserBundle:Categorietopics")->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($cat);
        return new JsonResponse($formatted);

    }

    public function updateTopicAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //  $user = $em->getRepository(User::class)->find($request->get('iduser'));
        $id = $request->get('id');
        $form = $em->getRepository('UserBundle:Topic')->find($id);


        if ($request->isMethod('POST')) {
            // $time = date("Y/m/d");

            $form->setTopicTitle($request->get('topicTitle'));
            $form->setTopicSubject($request->get('topicSubject'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($form);
            $em->flush();

            return new Response("OK",200);
        }
        return new Response("erreur",500);
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
            return $this->redirectToRoute("listFrontTopic");

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

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($cat);
        return new JsonResponse($formatted);
    }


    public function deleteCommentaireAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository('UserBundle:Reply')->find($id);
        $em->remove($cat);
        $em->flush();
        /*return $this->render("@Admin/Forum/listeCategorie",array(
            'Categorietopics'=>$cat
        ));*/
        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

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

        return $this->redirectToRoute("listFrontTopic");

    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts = $em->getRepository('UserBundle:Topic')->findEntitiesByStrings($requestString);
        if (!$posts) {
            $result['posts']['error'] = "Topic Not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($posts)
    {
        foreach ($posts as $posts) {
            $realEntities[$posts->getTopicId()] = [$posts->getPhoto(), $posts->getTopicTitle()];
        }
        return $realEntities;
    }



}