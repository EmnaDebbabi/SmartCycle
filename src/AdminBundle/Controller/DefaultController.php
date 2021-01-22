<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        return $this->render('@Admin/Default/index.html.twig');

    }
    public function countAction(){
        $em=$this->getDoctrine()->getManager();
        $query=$em->createQuery(" SELECT  e  FROM UserBundle:Commande e where e.etat = 1  " );



        $commandes= $query->getResult();
        $count=count($commandes);




        return $this->render('@Admin/Commande/count.html.twig' ,array(  'count'=>$count));

    }
}
