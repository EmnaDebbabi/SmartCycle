<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 27/03/2019
 * Time: 17:13
 */

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Association;
use UserBundle\Entity\Demande;
use UserBundle\Entity\Notification;
use UserBundle\Entity\Rating;
use UserBundle\Form\AssociationType;
use UserBundle\Form\RatingType;
use UserBundle\Repository\AssociationRepository;
use UserBundle\Repository\RatingRepository;
use Symfony\Component\HttpFoundation\File;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AssociationController extends Controller
{
    public function ajoutAction(Request $request){
        $asso = new Association();
        $form = $this->createForm(AssociationType::class,$asso);
        $form->handleRequest($request);
        if($form->isSubmitted()){

            $asso->setChefId($this->getUser());
            $asso->setCapital($request->get('capital'));
            $em = $this->getDoctrine()->getManager();
            $asso->uploadProfilePicture();


            $em->persist($asso);
            $em->flush();
            return $this->redirectToRoute("association_listmy");
        }
        return $this->render("@User/Association/add_association.html.twig",array(
            'form'=>$form->createView(),'asso'=>$asso
        ));

    }

    public function listAction(Request $request){
        $user=$this->getUser();
        $idu=$user->getId();

        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->findAll();
        $paginator = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $asso,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',6)
        );
        $result->setTemplate('@User/Paginator/pagination.html.twig');
        $msg = array();
        $rate = array();


        foreach($asso as $test ){
            $ida = $test->getIdAssociation();
            //$em->getRepository("UserBundle:Association")->findAcceptedAdherations($idu);
            $rateasso = $em->getRepository("UserBundle:Rating")->getAssociationsRatings($ida)[0]['val'];
            $demande=$em->getRepository("UserBundle:Demande")->findOneBy(array('idm'=>$idu,'ida'=>$ida));
            $rating=$em->getRepository("UserBundle:Rating")->findOneBy(array('idu'=>$idu,'ida'=>$ida));
            if(empty($demande)){$msg[]="demande access";}
            else{$msg[]="verifier demande";}
            if(empty($rating)){$rate[]=0;}
            else{$rate[]=$rateasso;}
        }


        return $this->render("@User/Association/list_association.html.twig",array( // nbadlou l test twig b list_association.html.twig
            'assos'=>$result ,'msgs'=>$msg,'rates'=>$rate
        ));
    }

    public function listmyAction(){
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->findBychefId($this->getUser());
        return $this->render("@User/Association/listmy_association.html.twig",array(
            'assos'=>$asso
        ));
    }

    public function updateAction(Request $request){

        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->find($id);
        $form = $this->createForm(AssociationType::class,$asso);
        $form->handleRequest($request);
        $img=$asso->getNomImage();
        if($form->isSubmitted()){
            $asso->uploadProfilePicture();
            $asso->setCapital($request->get('capital'));
            $asso->setChefId($this->getUser());

            $em->persist($asso);
            $em->flush();
            return $this->redirectToRoute("association_listmy");
        }

        return $this->render("@User/Association/update_association.html.twig",array(
            'form'=>$form->createView(),'asso'=>$asso ,'img'=>$img
        ));
    }

    public function deleteAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->find($id);
        $em->remove($asso);
        $em->flush();
        return $this->redirectToRoute("association_listmy");

    }

    public function inassociationAction(){
        $em = $this->getDoctrine()->getManager();
        $idu=$this->getUser()->getId();
        $asso = $em->getRepository("UserBundle:Association")->findAcceptedAdherations($idu);
        return $this->render("@User/Association/list_adherations.html.twig",array(
            'assos'=>$asso
        ));
    }

    public function associationAccessAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user=$this->getUser();
        $msg="";
        $idu=$user->getId();
        $asso = $em->getRepository("UserBundle:Association")->find($id);
        //$demande = new Demande();
        $demande=$em->getRepository("UserBundle:Demande")->findOneBy(array('idm'=>$idu,'ida'=>$id));
        if(empty($demande)){
            $demande = new Demande();
            $demande->setEtat("encour");
            $demande->setIda($asso);
            $demande->setIdm($user);
            $em->persist($demande);
            $em->flush();
// methode rapide pour creer des notifications
            $notif = new Notification();
            $notif->setIdm($em->getRepository("UserBundle:Membre")->find(5));
            $notif
                ->setTitle('nouveau demande d\'adhération !')
                ->setDescription($this->getUser()->getNom())
                ->setRoute('user_homepage')
                ->setParameters(array('ida'=> $id ,'idm'=> $idu));
            $em->persist($notif);
            $em->flush();

            $pusher = $this->get('mrad.pusher.notificaitons');
            $pusher->trigger($notif);
        } else if($demande->getEtat()=="encour"){$demande->setEtat("encours");}

        return $this->render("@User/Association/associationJoinRequest.html.twig",array(
            'asso'=>$asso,'demande'=>$demande
        ));
    }

    public function listadhAction(Request $request){
        $idu=$this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->findAcceptedAdherations($idu);
        return $this->render("@User/Association/list_adherations.html.twig",array(
            'assos'=>$asso
        ));

    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $assos =  $em->getRepository('UserBundle:Association')->findEntitiesByString($requestString);
        if(!$assos) {
            $result['assos']['error'] = "Aucun résultat d'association pour votre recherche :( ";
        } else {
            $result['assos'] = $this->getRealEntities($assos);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($assos){
        foreach ($assos as $assos){
            $realEntities[$assos->getIdAssociation()] = [$assos->getNomImage(),$assos->getNomAssociation()];
        }
        return $realEntities;
    }

    public function aboutAssociationAction(Request $request,$id){
        $user=$this->getUser();
        $idu=$user->getId();
        $em = $this->getDoctrine()->getManager();
        $rate=$em->getRepository("UserBundle:Rating")->findOneBy(array('idu'=>$idu,'ida'=>$id));
        //$rate= new Rating();
        $form = $this->createForm(RatingType::class,$rate);
        $form->handleRequest($request);

        $asso = $em->getRepository("UserBundle:Association")->find($id);
//        if($form->isSubmitted()){

//            if(empty($rate)){
                if($form->isSubmitted() && !empty($rate)){
                //$rate = new Rating();
                //$form = $this->createForm(RatingType::class,$rate);

                $rate->setIda($asso);
                $rate->setIdu($user);
                $rate->setValue($request->get('star'));
                $em->persist($rate);
                $em->flush();
            }

            else if($form->isSubmitted()){
                $rate = new Rating();
                $form = $this->createForm(RatingType::class,$rate);
                $form->handleRequest($request);
                    $rate->setIda($asso);
                    $rate->setIdu($user);
                $rate->setIda($asso);
                $rate->setIdu($user);
                    $rate->setValue($request->get('star'));
                    $em->persist($rate);
                    $em->flush();
                }

//            }


        return $this->render("@User/Association/aboutAssociation.html.twig" ,array(
            'asso'=>$asso,'form'=>$form->createView()
        ));

    }

    public  function carteAction(Request $request,$id){
        $snappy = $this->get('knp_snappy.pdf');
        $em=$this->getDoctrine()->getManager();
        $user = $this->getUser();
        $asso = $em->getRepository("UserBundle:Association")->find($id);
        $html = $this->renderView("@User/Association/carte.html.twig",array('asso'=>$asso,'user'=>$user));
        $filename = 'Carte d\'inscription';
        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }





}