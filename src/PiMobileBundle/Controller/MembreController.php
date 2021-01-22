<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 4/24/2019
 * Time: 6:51 PM
 */

namespace PiMobileBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Membre;
use DateTime;

class MembreController extends Controller
{
    public function allAction()
    {
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:Membre')
            ->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($tasks);
        return new JsonResponse($formatted);
    }
    public function findAction($email){
        $tasks = $this->getDoctrine()->getManager()
            ->getRepository('UserBundle:Membre')
            ->findOneBy(array('email'=>$email));
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($tasks);
        return new JsonResponse($formatted);
    }
    public function newAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = new Membre();
        $user -> setNom($request->get('nom'));
     //   $user -> setPrenom($request->get('prenom'));
      //  $user -> setSexe($request->get('sexe'));
        $sdt =$request->get('dateN');
        $dt = DateTime::createFromFormat("Y-m-d", $sdt);
        $user -> setDatedenaissance($dt);
        $user -> setPassword($request->get('password'));
        $user -> setAdresse($request->get('adresse'));
        $user -> setEmail($request->get('email'));
        $user -> setUsername($request->get('username'));
        $user -> setTelephone($request->get('telephone'));
        //$user -> setRoles(array(''));
        $user -> setGrade($request->get('grade'));
        $em->persist($user);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);
    }
    public function editAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user=$em->getRepository("UserBundle:Membre")->find($id);
        $user -> setUsername($request->get('username'));
        $user -> setEmail($request->get('email'));
      //  $user -> setNomImage($request->get('nomImage'));
        $user -> setPassword($request->get('password'));
        $user -> setNom($request->get('nom'));
        $user -> setAdresse($request->get('adresse'));
        $em->persist($user);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);
    }

}