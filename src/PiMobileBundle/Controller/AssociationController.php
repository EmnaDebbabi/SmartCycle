<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 23/04/2019
 * Time: 21:04
 */

namespace PiMobileBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\Association;
use UserBundle\Entity\Demande;
use UserBundle\Entity\Rating;
use UserBundle\Entity\Registre;
use UserBundle\Repository\AssociationRepository;
use UserBundle\Repository\RatingRepository;
use DateTime;

class AssociationController extends Controller
{
    public function allAction()
    {
        $em = $this->getDoctrine()->getManager();
        $assos = $em->getRepository('UserBundle:Association')->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($assos);
        return new JsonResponse($formatted);
    }

    public function myAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($request->get('chef'));
        $assos = $em->getRepository('UserBundle:Association')->findBy(array('chefId'=>$user));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($assos);
        return new JsonResponse($formatted);
    }

    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $asso = new Association();
        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $asso->setChefId($user);
        $asso->setNomAssociation($request->get('nom'));
        $asso->setAdresse($request->get('adresse'));
        $asso->setCapital($request->get('capital'));
        $asso->setIMG($request->get('image'));
        $asso->uploadProfilePicture();
        $em->persist($asso);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($asso);
        return new JsonResponse($formatted);
    }

    public function removeAction(Request $request)
    {   $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->find($request->get('ida'));
        $em->remove($asso);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($asso);
        return new JsonResponse($formatted);
    }
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Association")->findBy(array('nomAssociation'=>$request->get('nom')));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($asso);
        return new JsonResponse($formatted);
    }
    public function updateAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $asso=$em->getRepository("UserBundle:Association")->find($request->get('ida'));
        $asso->setNomAssociation($request->get('nom'));
        $asso->setAdresse($request->get('adresse'));
        $asso->setCapital($request->get('capital'));
        $em->persist($asso);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($asso);
        return new JsonResponse($formatted);

    }

    public function testdateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $reg = new Registre();
        $sdt =$request->get('date');
        $dt = DateTime::createFromFormat("Y-m-d", $sdt);
        $reg->setDate($dt);
        $reg->setContexte("contexte");
        $em->persist($reg);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reg);
        return new JsonResponse($formatted);

    }
    public function listdemandesAction(){

    }

    public function newdemandeAction(Request $request){
        $em = $this->getDoctrine()->getManager();


        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $asso=$em->getRepository("UserBundle:Association")->find($request->get('idasso'));
        $demande=$em->getRepository("UserBundle:Demande")->findOneBy(array('idm'=>$user,'ida'=>$asso->getIdAssociation()));
        if(empty($demande)){
            $demande = new Demande();
            $demande->setIdm($user);
            $demande->setIda($asso);
            $demande->setEtat("valider");
            $em->persist($demande);
            $em->flush();
        }
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($demande);
        return new JsonResponse($formatted);
    }

    public function cancelDemandeAction(Request $request){
        $em = $this->getDoctrine()->getManager();


        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $asso=$em->getRepository("UserBundle:Association")->find($request->get('idasso'));
        $demande=$em->getRepository("UserBundle:Demande")->findOneBy(array('idm'=>$user,'ida'=>$asso->getIdAssociation()));
        $em->remove($demande);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($demande);
        return new JsonResponse($formatted);
    }

    public function listadhAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $asso = $em->getRepository("UserBundle:Association")->findAcceptedAdherations($user->getId());
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($asso);
        return new JsonResponse($formatted);

    }

    public function buttonNameAction(Request $request){
        $var = array('btname'=>'Acceder');
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $asso=$em->getRepository("UserBundle:Association")->find($request->get('idasso'));
        $demande=$em->getRepository("UserBundle:Demande")->findOneBy(array('idm'=>$user,'ida'=>$asso->getIdAssociation()));
        if(empty($demande)) {
            $var = array('btname'=>'Adherer');
        }
        $btName = array($var);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($btName);
        return new JsonResponse($formatted);

    }
    public function rateAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($request->get('membre'));
        $idu=$user->getId();
        $rate=$em->getRepository("UserBundle:Rating")->findOneBy(array('idu'=>$idu,'ida'=>$request->get('idasso')));
        $asso = $em->getRepository("UserBundle:Association")->find($request->get('idasso'));
        if(!empty($rate)){
            $rate->setIda($asso);
            $rate->setIdu($user);
            $rate->setValue($request->get('star'));
            $rate->setDescription($request->get('desc'));
            $em->persist($rate);
            $em->flush();
        } else {
            $rate = new Rating();
            $rate->setIda($asso);
            $rate->setIdu($user);
            $rate->setValue($request->get('star'));
            $rate->setDescription($request->get('desc'));
            $em->persist($rate);
            $em->flush();
        }
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($rate);
        return new JsonResponse($formatted);
    }

}