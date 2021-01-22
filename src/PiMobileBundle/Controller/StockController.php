<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 4/27/2019
 * Time: 2:20 PM
 */

namespace PiMobileBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Stock;

class StockController extends Controller
{

    public function allAction()
    {
        $em=$this->getDoctrine()->getManager();
        $query=$em->createQuery(" SELECT e FROM UserBundle:Stock e WHERE e.quantitedispo > 0 " );


        $stock= $query->getResult();
        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($stock);
        return new JsonResponse($formatted);
    }




}