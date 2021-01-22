<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 4/10/2019
 * Time: 10:17 PM
 */

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\Commande;
use UserBundle\Entity\Panier;

class CommandeController extends Controller
{
    public function CommandeAction(Request $request)
    {
        $msg2="Votre commande est réservée pendant 10 jours. Au-delà de ce délai, sans réception de paiement votre commande sera annulée. ";
        $msg3="Un rappel de paiement sera envoyée  par SMS  " ;

        $var="paye";
        $em = $this->getDoctrine()->getManager();

        $id=$this->getUser()->getId();

        $query=$em->createQuery(" SELECT e FROM UserBundle:Commande e WHERE e.idUser=:id and e.etat = 1");
        $query->setParameter('id',$id);

        $cmd= $query->getResult();

        return $this->render("@User/ClientEntreprise/commande.html.twig",array(
            'cmd'=>$cmd  ,'msg2'=>$msg2,'msg3'=>$msg3 ,'var'=>$var
        ));
    }

    public function PointAction()
    {

        $user = $this->getUser();
        $id = $user->getId();
        $pointcal='calcule';
        $em = $this->getDoctrine()->getManager();
//        $query=$em->createQuery("select c ,Sum(c.total) as sss from  UserBundle:Commande c  where c.idUser=:id and c.payement = :paye and c.point is NULL ");
//        $query->setParameters(array('id'=>$id,'paye'=>"paye"));
//        $commandes= $query->getResult();
//        $sum = $commandes[0]['sss'];
//        $point = $sum/10 ;
        ///////////////////commandes////////////////
        $query2=$em->createQuery("select c from  UserBundle:Commande c  where c.idUser=:id and c.payement = :paye and c.point is NULL ");
        $query2->setParameters(array('id'=>$id,'paye'=>"paye"));
        $commandes2= $query2->getResult();
        foreach ($commandes2 as $x)
        {
            $x->setPoint('calcule');
            $em->persist($x);
            $em->flush();

        }
     //////////////////////////////



        $query3=$em->createQuery("select c ,Sum(c.total) as sss from  UserBundle:Commande c  where c.idUser=:id and c.payement = :paye and c.point = :calcule ");
        $query3->setParameters(array('id'=>$id,'paye'=>"paye",'calcule'=>$pointcal));
        $commandes3= $query3->getResult();
        $sum3 = $commandes3[0]['sss'];
        $point2 = $sum3/10 ;

        return $this->render("@User/ClientEntreprise/point.html.twig",array(
           'point'=>$point2
        ));




    }

    public function accederaupointAction()
    {
        $user = $this->getUser();
        $id = $user->getId();
        $etat=0;
        $pointcal='calcule';
        $em = $this->getDoctrine()->getManager();

        ///////////////////////////////////////
        $query=$em->createQuery("select c ,Sum(c.total) as sss from  UserBundle:Commande c  where c.idUser=:id and c.payement = :paye and c.point = :calcule ");
        $query->setParameters(array('id'=>$id,'paye'=>"paye",'calcule'=>$pointcal));
        $commandes= $query->getResult();
        $sum = $commandes[0]['sss'];
        $point = $sum/10 ;
        /// ////////////////////////
        $commandes2=$em->getRepository("UserBundle:Commande")->findBy(array("idUser" => $user, "point" => $pointcal));

        ///////////////////////////////////////////////////
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));
        $random=random_int(1,4);
        $stock=$em->getRepository("UserBundle:Stock")->find($random);


        $kilo=round($point/100)*5;



       if ($point >= 100)
        {
            if(empty($CMD1))
            {
                $CMD1 = new Commande();
                $CMD1->setIdUser($this->getUser());
                $etat = 0;
                $CMD1->setEtat($etat);
                $em->persist($CMD1);
                $em->flush();
            }

            ///////////////stock
            $qtestock = $stock->getQuantitedispo();
            $qterestant = $qtestock - $kilo;
            while ($qterestant < 0)
            {
                $random = random_int(1, 4);
                $stock = $em->getRepository("UserBundle:Stock")->find($random);

                $kilo = round($point / 100) * 5;

                $qtestock = $stock->getQuantitedispo();
                $qterestant = $qtestock - $kilo;
            }
            $prix = $stock->getPrixuni();
//            $id = $stock->getIdCat();
//                $total = $kilo * $prix;
            $stock->setQuantitedispo($qterestant);
            $em->persist($stock);
            $em->flush();
//////////////////////////////////////
            $Lignecommande = $em->getRepository('UserBundle:Panier')->findOneby(array("idStock" => $stock->getIdCat(),"idcommande"=>$CMD1->getIdCmd()));
            if (empty($Lignecommande))
            {
                $ligneCMD = new Panier();
                $ligneCMD->setIdStock($stock);
                $ligneCMD->setIdcommande($CMD1);
                $ligneCMD->setPrix($prix);
                $ligneCMD->setQuantite($kilo);
                $ligneCMD->setTotal(0);
                $em->persist($ligneCMD);
                $em->flush();
            }
            else
            {

                $Lignecommande->setPrix($prix);
                $Lignecommande->setQuantite($kilo);
                $Lignecommande->setTotal(0);
                $em->persist( $Lignecommande);
                $em->flush();





            }
           foreach ($commandes2 as $x)
           {
               $x->setPoint('calculedeja');
               $em->persist($x);
               $em->flush();

           }
           $msg='';
         $msg2="Félecitations , Vous gagnez avec SMART CYCLE de kilo gratuit ! Consultez votre panier ";
           return $this->render("@User/ClientEntreprise/cadeaux.html.twig",array(
               'point'=>$point,'message'=>$msg ,'Message2'=>$msg2
           ));
        }
        else
        {
            $msg2='';
            $msg ="votre point est inférieur à 100 point ";
            return $this->render("@User/ClientEntreprise/cadeaux.html.twig",array(
                'point'=>$point,'message'=>$msg ,'Message2'=>$msg2
            ));
        }




        $msg2='';
        $msg1='';
        return $this->render("@User/ClientEntreprise/point.html.twig",array(
            'point'=>$point,'message'=>$msg ,'Message2'=>$msg2
        ));





    }

    public function paiementAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('idCmd');
        $user = $this->getUser();
        $idu = $user->getId();
        $etat = 1 ;
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu,"idCmd"=>$id ,"etat"=>$etat));
        $CMD1->setPayement("paye");
        $em->persist($CMD1);
        $em->flush();
       $msg="le paiement effectué avec succès ";
       \Stripe\Stripe::setApiKey('sk_test_zHJBDy5ZMqeGIRhhrR821JIT00pUPZpUkH');

      \Stripe\Charge::create(

           array(

               'amount' => 2000,

               'currency' => 'usd',

                'source' => 'tok_visa' ,
                'description'=>'paiement de commande'


));
        return $this->render("@User/ClientEntreprise/paiement.html.twig" ,array('msg'=>$msg,'cmd'=>$CMD1));
       ;

    }

    public function passerauPayementAction(Request $request)
    {

        $msg='';
        $em = $this->getDoctrine()->getManager();
        $idcmd = $request->get('idCmd');
        $user = $this->getUser();
        $idu = $user->getId();
        $etat = 1 ;
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu,"idCmd"=>$idcmd ,"etat"=>$etat));


        return $this->render("@User/ClientEntreprise/paiement.html.twig",array('msg'=>$msg ,'cmd'=>$CMD1));
    }
}