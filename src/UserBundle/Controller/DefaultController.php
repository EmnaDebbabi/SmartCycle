<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
        $user=$this->getUser();
//        $id=$user->getId();
//        $etat = 0;
//        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));
//
//        $query=$em->createQuery("select p from UserBundle:Panier p join UserBundle:Commande c with c.idCmd =p.idcommande where c.idUser=:id");
//        $query->setParameter('id',$id);
//
//        $panier= $query->getResult();

        return $this->render('@User/Default/homeuser.html.twig' ,array('user'=>$user));
    }

}
