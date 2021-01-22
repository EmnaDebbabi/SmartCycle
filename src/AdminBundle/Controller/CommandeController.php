<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 4/12/2019
 * Time: 7:21 PM
 */

namespace AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Panier;
use UserBundle\Entity\Commande;


class CommandeController extends Controller
{

    public function AfficherCommandesAction(Request $request)
    {     $datej=  new \DateTime('now');
        $em=$this->getDoctrine()->getManager();
        $query=$em->createQuery(" SELECT e FROM UserBundle:Commande e where e.etat =1 order by e.dateexp DESC  " );

        $count ='';

        $commandes= $query->getResult();
        return $this->render("@Admin/Commande/show.html.twig",array(
            'commandes'=>$commandes , 'count'=>$count,'datej'=>$datej
        ));
    }
    public function EndetailAction(Request $request)
    { $count ='';
        $id=$request->get( 'idCmd');
        $em= $this->getDoctrine()->getManager();


        $Lignecommande=$em->getRepository("UserBundle:Panier")->findBy(array("idcommande"=>$id));
        return $this->render('@Admin/Commande/VoirEnDetail.html.twig',array("panier"=>$Lignecommande , 'count'=>$count ));
    }
}