<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 3/27/2019
 * Time: 3:49 PM
 */

namespace UserBundle\Controller;
use UserBundle\Entity\Cadeau;
use UserBundle\Entity\Evttesting;
use UserBundle\Entity\Ratinge;
use UserBundle\Entity\Membre;
use UserBundle\Form\EvttestingType;
use UserBundle\Form\RatingeType;
use UserBundle\Form\CadeauType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File;
use UserBundle\Entity\Participant;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Date;

class EventsController extends Controller
{
    ////Add Event
    public function addEventAction(Request $request)
    {
        $evttesting = new Evttesting();
        $form=$this->createForm(EvttestingType::class,$evttesting);
        ///Pour recuperer les entrees de la form comme post dans le
        $form->handleRequest($request);
        if($form->isSubmitted()){
              $evttesting->setIdm($this->getUser());
            $sdt =$request->get('date');
            $dt = DateTime::createFromFormat("Y-m-d", $sdt);
            //On recupere l'EntityManager
            $em=$this->getDoctrine()->getManager();
//            $dt=date('Y-m-d',$request->get('date'));
            $evttesting->setDate($dt);
            $evttesting->uploadProfilePicture();

            //On persiste l'entite
            $em->persist($evttesting);
            ///for the execution
            //On flush ce qui a ete persiste avant
            $em->flush();
//            dump($form->getErrors(true));
//            die();
            ///for showing redirection
          return $this->redirectToRoute("show_MyEvent");
        }
        ///
        return $this->render("@User/Events/addEvent.html.twig",array(
            'form'=>$form->createView()
        ));

    }

    //Show all events
    public function showEventAction(Request $request){
        $id=$this->getUser()->getId();
        $user=$this->getUser();
        $idu=$user->getId();
        $em=$this->getDoctrine()->getManager();

        $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);

        $msg="Participer pour nous joindre";
        $paginator  = $this->get('knp_paginator');

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 6)/*limit per page*/
        );
        $events->setTemplate('@User/Paginator/pagination.html.twig');
        ////
        $rate = array();


        foreach($events as $test ){
            $ide = $test->getId();
            //$em->getRepository("UserBundle:Association")->findAcceptedAdherations($idu);
            $rateve = $em->getRepository("UserBundle:Ratinge")->getEventsRatings($ide)[0]['val'];
//            $demande=$em->getRepository("UserBundle:")->findOneBy(array('idm'=>$idu,'ida'=>$ide));
            $rating=$em->getRepository("UserBundle:Ratinge")->findOneBy(array('idu'=>$idu,'ide'=>$ide));

            if(empty($rating)){$rate[]=0;}
            else{$rate[]=$rateve;}
        }
        ///

        return $this->render("@User/Events/showEvent.html.twig",array(
            'events'=>$events,'MessageEvent'=>$msg,'rates'=>$rate
        ));
    }

    //Show my events
    public function showMyEventAction(){
        $id=$this->getUser()->getId();
        $msg=" ";
        $msg1=" ";
        $em=$this->getDoctrine()->getManager();
        //   $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        $query = $em->createQuery(" SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id ");

        $query->setParameter('id',$id);

        $events = $query->getResult();

        $nb=count($events);
        return $this->render("@User/Events/showMyEvent.html.twig",array(
            'events'=>$events,'MessageEvent'=>$msg,'Message1'=>$msg1,'nb'=>$nb
        ));
    }
///Remove my Event
    public function RemoveEventAction(Request $request)
    {

       $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $evttesting=$em->getRepository("UserBundle:Evttesting")->find($id);
        $em->remove($evttesting);
        $em->flush();
        return $this->redirectToRoute("show_MyEvent");
    }
    //Update My Event
    public function updateEventAction(Request $request){
        $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $evttesting=$em->getRepository("UserBundle:Evttesting")->find($id);
       $img=$evttesting->getNomImage();
        $nom=$evttesting->getNom();

        $date=$evttesting->getDate();
        $fot = $date->format('Y-m-d');
        $form=$this->createForm(EvttestingType::class,$evttesting);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $sdt =$request->get('date');
            $dt = DateTime::createFromFormat("Y-m-d", $sdt);
            $evttesting->setDate($dt);
            $evttesting->setIdm($this->getUser());
            $evttesting->uploadProfilePicture();
            $em->persist($evttesting);
            $em->flush();
            return $this->redirectToRoute("show_MyEvent");
        }
        return $this->render("@User/Events/updateMyEvent.html.twig",array(
            'form'=>$form->createView(),'evttesting'=>$evttesting ,"img"=>$img,"nom"=>$nom,"fot"=>$fot));
    }
/////
    //Show my participation
    public function showMyParticipationAction(Request $request){
        $msg1="Cliquez sur Avis pour nous donner votre avis ";
        $msg2="Cliquez sur annuler si vous souhaitez annuler votre participation ";
        $id=$this->getUser()->getId();
        $etat="interssé";
        $em=$this->getDoctrine()->getManager();
        $query = $em->createQuery(" SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");

        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();

//       $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        $msg="Participer pour nous joindre";
        return $this->render("@User/Events/showMyParticipation.html.twig",array(
            'events'=>$events,'MessageEvent1'=>$msg1,'MessageEvent2'=>$msg2
        ));
    }
/////Consulter mes points cadeaux
public function PointsCadeauxAction(Request $request){
        $msg1="";
    $msg2="";
    $result=0;
    $id=$this->getUser()->getId();
    $etat="interssé";
    $criteria=$request->get('search');
    $em=$this->getDoctrine()->getManager();
    $query = $em->createQuery("SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");

    $query->setParameters(array('id'=>$id,'etat'=>$etat));

    $events = $query->getResult();

    $nbparti=count($events);
    if ($nbparti<=2){$msg1="Vous n'êtes pas assez actif! Participez de plus avec nous";}
else if($nbparti>2 && $nbparti<5 ){$msg2="Vous êtes proche de ganger avec nous! Participez davantage";}
    if ($nbparti>=5) {
           if($request->isMethod('POST')) {
            $codes=$em->getRepository(Cadeau::class)->findBy(array('codecadeau' => $criteria));
            $result=count($codes);
                if ( $result !=0){
            $participation = $em->getRepository("UserBundle:Participant")->findBy(array('idm' => $id));
            foreach ($participation as $pa) {
                $pa->setEtat("valider");
                $em->persist($pa);
                $em->flush();
                $msg2="Bravo! Vous êtes ami de la nature,Cliquez pour obtenir ton cadeau";
            }

        }
else $msg2="votre code est erronné!Veuillez entrer un code valide";
        }
    }
    return $this->render("@User/Events/cadeauxParticipation.html.twig",array(
        'events'=>$events,'MessageEvent1'=>$msg1,'MessageEvent2'=>$msg2,'MessageEvent'=>$nbparti,'Result'=>$result
    ));
}
    ////Consulter Code
    public function CodeCadeauAction(){
        $id=$this->getUser()->getId();
        $nom=$this->getUser()->getNom();
        return $this->render("@User/Events/CodeCadeau.html.twig",array('iduser'=>$id,'nomuser'=>$nom

        ));

    }
    ///Save as html

    public function PdfCadeauAction( Request $request){
        $snappy = $this->get('knp_snappy.pdf');
        $msg='Votre QR code cadeau offert par SmartCycle';
        $em = $this->getDoctrine()->getManager();
//      $date= new Date();
//        $dt = DateTime::createFromFormat("Y-m-d", new Date());
        $time = new \DateTime();
   // $time->format('H:i:s \O\n Y-m-d');
   //     $dt = DateTime::createFromFormat("H:i:s \O\n Y-m-d", $time);
//        $date->format('Y-m-d H:i:s');
        ////////
//        $idcmd = $request->get('idCmd');
        $user = $this->getUser();
        $idu = $user->getId();
        $user = $em->getRepository("UserBundle:Membre")->find($idu);
        $query=$em->createQuery("select p from UserBundle:Participant p join UserBundle:Evttesting e with e.id=p.idevent where p.idm=:id ");
        $query->setParameters(array('id'=>$idu ));

        $gagnant= $query->getResult();
        //////////

        $html = $this->renderView('@User/Events/imprimeCode.html.twig', array("gagnant" => $user, "Message" => $msg
            //..Send some data to your view if you need to //
        ));

        $filename = 'CodeCadeau';
        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );

    }


 /////Annuler ma participation
    public function AnnulerParticipationAction(Request $request)
    {

        $msg1  =  'Votre participation a été annulé avec succés!';

        $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $participation= new Participant();
        $participation=$em->getRepository("UserBundle:Participant")->findOneByidevent($id);
            $participation->setEtat("non interssé");
        $em->persist($participation);
        $em->flush();
        $etat="interssé";

//        return $this->redirectToRoute("show_MyParticipation");

        return $this->redirectToRoute("show_MyParticipation");
    }
    /////////Rechercher Events
    public function rechercherEventAction(Request $request)
    {
        $msg="Participer pour nous joindre";
        $criteria=$request->get('search');
        $em=$this->getDoctrine()->getManager();
        if($request->isMethod('POST')) {
            $events = $em->getRepository("UserBundle:Evttesting")->findBy(array('type' => $criteria));
        }
        return $this->render("@User/Events/showEventt.html.twig",array(
                 'events'=>$events,'MessageEvent'=>$msg
             ));
}
////Donner Avis
    public function AvisEventAction(Request $request){
        ////
        $em= $this->getDoctrine()->getManager();
        $user=$this->getUser();
        $idu=$user->getId();

        $id=$request->get('id');

        $Event=$em->getRepository('UserBundle:Evttesting')->find($id);
        $participation=$em->getRepository('UserBundle:Participant')->findOneBy(array('idm'=>$idu,'idevent'=>$id));
//        $enseignes = $em->getRepository("ClientBundle:Participation")->findAll();

        $datej=new \DateTime('now');

        $week=date("Y-m-d", strtotime("-1 week"));

        $query = $em->createQuery(
            'SELECT e
    FROM UserBundle:Evttesting e
    WHERE e.date >=:d1 '
        );
        $query->setParameter('d1',$week);

        $events = $query->getResult();

        if($request -> isMethod("post")){
            $etat=$participation->getEtat();
            $msg='participant';
            $participation->setAvis($request->get('avis'));
            $participation-> setIdM($user);
            $participation-> setEtat($etat);
            $participation-> setIdevent($Event);
            $em->persist($participation);
            $em->persist($Event);
            $em->flush();
            $em=$this->getDoctrine()->getManager();
            $events=$em->getRepository(Evttesting::class)->findAll();
            return $this->redirectToRoute('show_MyParticipation',array('MessageEvent'=>$msg,'events'=>$events));
        }

        $msg  =  'Déjà participant! Cliquez pour annuler!';

        return $this->render('@User/Events/avisEvent.html.twig',array('MessageEvent'=>$msg,'events'=>$events));


    }
    /////Recherche Ajax
//    public function rechercheEventAjaxDQLAction(Request $request)
//    {
//        if ($request->isXmlHttpRequest()) {
//            $nom = $request->get('nom');
//            $em = $this->getDoctrine()->getManager();
//            $E = $em->getRepository(Evttesting::class)->findEventDQL($nom);
//            $ser = new Serializer(array(new ObjectNormalizer()));
//            $data = $ser->normalize($E);
//            return new JsonResponse($data);
//        }
//        return $this->render('@User/Events/showEvent.html.twig.html.twig', array());
//    }
public function searchAction(Request $request)
{
    $em = $this->getDoctrine()->getManager();
    $requestString = $request->get('q');
    $posts =  $em->getRepository('UserBundle:Evttesting')->findEntitiesByString($requestString);
    if(!$posts) {
        $result['posts']['error'] = "Event Not found :( ";
    } else {
        $result['posts'] = $this->getRealEntities($posts);
    }
    return new Response(json_encode($result));
}
    public function getRealEntities($posts){
        foreach ($posts as $posts){
            $realEntities[$posts->getId()] = [$posts->getNomImage(),$posts->getNom()];
        }
        return $realEntities;
}

    /////////Show Event Details
    public function detailEventAction(Request $request){
        $id=$request->get('id');
        $idu=$this->getUser()->getId();
        $em=$this->getDoctrine()->getManager();
        $evttesting=$em->getRepository("UserBundle:Evttesting")->find($id);
        $rate=$em->getRepository("UserBundle:Ratinge")->findOneBy(array('idu'=>$idu,'ide'=>$id));
        $form = $this->createForm(RatingeType::class,$rate);
        $form->handleRequest($request);
        $img=$evttesting->getNomImage();
        $nom=$evttesting->getNom();
        $date=$evttesting->getDate();
        $fot = $date->format('Y-m-d');
//
//            $sdt =$request->get('date');
//            $dt = DateTime::createFromFormat("Y-m-d", $sdt);
//            $evttesting->setDate($dt);
       //     $evttesting->setIdm($this->getUser());
            $evttesting->uploadProfilePicture();
            $em->persist($evttesting);
            $em->flush();
            //return $this->redirectToRoute("show_Event");
        ////
        if($form->isSubmitted() && !empty($rate)){
            //$rate = new Rating();
            //$form = $this->createForm(RatingType::class,$rate);

            $rate->setIde($evttesting);
            $rate->setIdu($this->getUser());
            $rate->setValue($request->get('star'));
            $em->persist($rate);
            $em->flush();
        }

        else if($form->isSubmitted()){
            $rate = new Ratinge();
            $form = $this->createForm(RatingeType::class,$rate);
            $form->handleRequest($request);
            $rate->setIde($evttesting);
            $rate->setIdu($this->getUser());
//            $rate->setIda($evttesting);
//            $rate->setIdu($this->getUser());
            $rate->setValue($request->get('star'));
            $em->persist($rate);
            $em->flush();
        }
        return $this->render("@User/Events/detailsEvents.html.twig",array(
            'evttesting'=>$evttesting ,"nom"=>$nom,"fot"=>$fot,'form'=>$form->createView()));
    }
///fos Message bundle pour chat
    public function MessageAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $users=$em->getRepository(Membre::class)->findAll();
        return $this->render('FOSMessageBundle/layout.html.twig',array('users'=>$users));
    }
    ///fos Message bundle pour chat users_connectes
    public function usersConnectedEventAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $users=$em->getRepository(Membre::class)->findAll();
        return $this->render('@User/Events/usersconnected.html.twig',array('users'=>$users));
    }
}