<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 4/27/2019
 * Time: 1:36 AM
 */

namespace PiMobileBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Evttesting;
use UserBundle\Entity\Participant;
use UserBundle\Entity\Cadeau;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use DateTime;

class EventsController extends Controller
{
    public function allEventsAction()
    {
        $events = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:Evttesting')
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($events);
        return new JsonResponse($formatted);
    }
    //Show my events
    public function showMyEventAction($id){
        $msg=" ";
        $msg1=" ";
        $em=$this->getDoctrine()->getManager();
        //   $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        $query = $em->createQuery(" SELECT e FROM UserBundle:Evttesting e WHERE e.idm=:id ");
        $query->setParameter('id',$id);
        $events = $query->getResult();
        $nb=count($events);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($events);
        return new JsonResponse($formatted);
    }
    public function newEventAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = new Evttesting();
//    $ev=$em->getRepository("UserBundle:Membre")->find($request->get('id'));
        $user=$em->getRepository("UserBundle:Membre")->find($id);
       // $user=$em->getRepository("UserBundle:Membre")->find('id');
        $event->setIdm($user);
        $event->setNom($request->get('nom'));
        $event->setType($request->get('type'));
        $event->setLieu($request->get('lieu'));
        $event->setDescription($request->get('description'));
        $event->setHeure($request->get('heure'));
        $event->setNomImg($request->get('nomImage'));
//        $datej=new \DateTime('now');
//        $event->setDate($datej);
        $sdt =$request->get('date');
        $dt = DateTime::createFromFormat("Y-m-d", $sdt);
        $event -> setDate($dt);
        $event->uploadProfilePicture();

        $em->persist($event);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($event);
        return new JsonResponse($formatted);
    }
    public function findEventAction($id)
    {
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:Evttesting')
            ->find($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($tasks);
        return new JsonResponse($formatted);

    }
    public function removeEventAction($id)
    {   $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("UserBundle:Evttesting")->find($id);
        $em->remove($event);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($event);
        return new JsonResponse($formatted);
    }
    public function chercherEventAction($titre)
    {
        $em = $this->getDoctrine()->getManager();
        $Evenement = $em->getRepository("UserBundle:Evttesting")->findBy(array('type'=>$titre));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($Evenement);
        return new JsonResponse($formatted);
    }
    public function updateEventAction(Request $request,$id,$idu)
    {
        $em=$this->getDoctrine()->getManager();
       $user=$em->getRepository("UserBundle:Membre")->find($idu);
        $event=$em->getRepository("UserBundle:Evttesting")->find($id);
        $event->setNom($request->get('nom'));
        $event->setLieu($request->get('lieu'));
        $event->setType($request->get('type'));
        $event->setDescription($request->get('description'));
        $event->setHeure($request->get('heure'));
       //$event->setIdm(40);
        $datej=new \DateTime('now');
      //  $event->setDate($datej);
        $event->setIdm($user);
        $em->persist($event);
        $em->flush();
        return new JsonResponse("success");

    }
    public function ParticiperAction(Request $request,$idevent,$idm)
    {
        ////
        $em= $this->getDoctrine()->getManager();
       // $user=$this->getUser();
        $user=$em->getRepository("UserBundle:Membre")->find($idm);
      //  $idu=$user->getId();
$idu=40;
      $id=$request->get('id');


        $Event=$em->getRepository('UserBundle:Evttesting')->find($idevent);
        $participation=$em->getRepository('UserBundle:Participant')->findOneBy(array('idm'=>$idm,'idevent'=>$idevent));
//        $enseignes = $em->getRepository("ClientBundle:Participation")->findAll();

        $datej=new \DateTime('now');

        $week=date("Y-m-d", strtotime("-1 week"));

        $query = $em->createQuery('SELECT e FROM UserBundle:Evttesting e WHERE e.date >=:d1 ');
        $query->setParameter('d1',$week);

        $events = $query->getResult();

        if(empty($participation)){
            $msg='participant';
            $participation=new Participant();
            $participation->setEtat("interssé");
            $participation->setAvis("pas encore");
            $participation-> setIdM($user);
            $participation-> setIdevent($Event);
            $em->persist($participation);
            $em->persist($Event);
            $em->flush();
            $em=$this->getDoctrine()->getManager();
            $etat="interssé";
            $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);
            return new JsonResponse("Participation Done");
        }

        $msg  =  'Déjà participant! Cliquez pour annuler!';
        $em=$this->getDoctrine()->getManager();
        $participation->setEtat("non interssé");
        $em->persist($Event);
        $em->flush();
        $etat="non interssé";
      $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);
//        $serializer = new Serializer([new ObjectNormalizer()]);
//        $formatted = $serializer->normalize($events);
        return new JsonResponse("Participation annuler");

    }
    //Show all events
    public function showAllEvents2Action(Request $request,$id){
     //   $id=$this->getUser()->getId();
      //  $user=$this->getUser();
       // $idu=$user->getId();
        $em=$this->getDoctrine()->getManager();

        $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);

        $msg="Participer pour nous joindre";

        ///
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($events);
        return new JsonResponse($formatted);

    }
    //Show my participation
    public function showMyParticipationAction(Request $request,$id){
        $msg1="Cliquez sur Avis pour nous donner votre avis ";
        $msg2="Cliquez sur annuler si vous souhaitez annuler votre participation ";
        $etat="interssé";
        $em=$this->getDoctrine()->getManager();
        $query = $em->createQuery(" SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");

        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($events);
        return new JsonResponse($formatted);

    }
    ////Donner Avis
    public function AvisEventAction(Request $request,$idu,$id){
        ////
        $em= $this->getDoctrine()->getManager();
       //$user=$this->getUser();
       // $idu=$user->getId();
        $user=$em->getRepository("UserBundle:Membre")->find($idu);

      // $id=$request->get('id');

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
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($events);
            return new JsonResponse("avis ajoute");

    }
    public function findParticipantAction($idevent,$idu)
    {

        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:Participant')
            ->findBy(array('idm'=>$idu,'idevent'=>$idevent));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($tasks);
        return new JsonResponse($formatted);

    }
    /////Consulter mes points cadeaux
    public function PointsCadeauxAction(Request $request,$idu){
        $msg1="";
        $msg2="";
        $result=0;
        $etat="interssé";
     //   $criteria=$request->get('search');
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($idu);
        $id=$user->getId();
        $query = $em->createQuery("SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");
        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();

        $nbparti=count($events);

        $info = array(
            'info' => $nbparti
        );
        $list =array($info);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($list);
        return new JsonResponse($formatted);
    }
    /////Consulter mes points cadeaux
    public function CodeCadeauxAction(Request $request,$idu,$criteria){
        $msg1="";
        $msg2="";
        $result=0;
        $etat="interssé";
        //   $criteria=$request->get('search');
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($idu);
        $id=$user->getId();
        $query = $em->createQuery("SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");
        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();

        $nbparti=count($events);

        $info = array(
            'info' => $nbparti
        );
        $list =array($info);

        if ($nbparti<=2){$msg1="Vous n'êtes pas assez actif! Participez de plus avec nous";}
        else if($nbparti>2 && $nbparti<=4 ){$msg2="Vous êtes proche de ganger avec nous! Participez davantage";}
        if ($nbparti>=5) {
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
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($list);
        return new JsonResponse($formatted);
    }
    /////verifier code cadeaux
    public function VerifierCodeCadeauxAction(Request $request,$idu,$criteria){
        $msg1="";
        $msg2="";
        $result=0;
        $etat="interssé";
        //   $criteria=$request->get('search');
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($idu);
        $id=$user->getId();
        $query = $em->createQuery("SELECT e FROM UserBundle:Evttesting e JOIN UserBundle:Participant p WITH p.idevent = e.id WHERE p.idm=:id AND p.etat like :etat");
        $query->setParameters(array('id'=>$id,'etat'=>$etat));

        $events = $query->getResult();

        $nbparti=count($events);

        $codes=$em->getRepository(Cadeau::class)->findBy(array('codecadeau' => $criteria));
        $result=count($codes);
        $info = array(
            'verifcode' => $result
        );
        $list =array($info);

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($list);
        return new JsonResponse($formatted);
    }
    ///CollecteStat
    public function StatCollecteAction()
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $produits = $em->getRepository("UserBundle:Evttesting")->findAll();
        $tab = array();
        $categories = array();

        $nbF = 0;
        $nbH = 0;
        $nbE = 0;
        $nbA = 0;
        foreach ($produits as $pd) {
            if ($pd->getType() == "Collecte") {

                $nbF =$nbF + 1;
                array_push($categories, "Collecte");
            }
        }


        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($nbF);
        return new JsonResponse($formatted);

    }
    ///RecyclageStat
    public function StatRecyclageAction()
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $produits = $em->getRepository("UserBundle:Evttesting")->findAll();
        $tab = array();
        $categories = array();

        $nbF = 0;
        $nbH = 0;
        $nbE = 0;
        $nbA = 0;
        foreach ($produits as $pd) {
            if ($pd->getType() == "Recyclage") {

                $nbF =$nbF + 1;
                array_push($categories, "Recyclage");
            }
        }


        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($nbF);
        return new JsonResponse($formatted);

    }
    ///SensibilisationStat
    public function StatSensibilisationAction()
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $produits = $em->getRepository("UserBundle:Evttesting")->findAll();
        $tab = array();
        $categories = array();

        $nbF = 0;
        $nbH = 0;
        $nbE = 0;
        $nbA = 0;
        foreach ($produits as $pd) {
            if ($pd->getType() == "sensibilisation") {

                $nbF =$nbF + 1;
                array_push($categories, "sensibilisation");
            }
        }


        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($nbF);
        return new JsonResponse($formatted);

    }
    ///NatureStat
    public function StatNatureAction()
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $produits = $em->getRepository("UserBundle:Evttesting")->findAll();
        $tab = array();
        $categories = array();

        $nbF = 0;
        $nbH = 0;
        $nbE = 0;
        $nbA = 0;
        foreach ($produits as $pd) {
            if ($pd->getType() == "Nature") {
                $nbF =$nbF + 1;
                array_push($categories, "Nature");
            }
        }


        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($nbF);
        return new JsonResponse($formatted);

    }
}