<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 3/30/2019
 * Time: 5:33 PM
 */

namespace UserBundle\Controller;
use UserBundle\Entity\Commande;
use UserBundle\Entity\Panier;
use UserBundle\Entity\Stock;
use UserBundle\Repository\PanierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime ;


class PanierController extends  Controller
{

    public function ajouterlignecommandeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $stock= $em->getRepository("UserBundle:Stock")->findAll();

        if ($request->isMethod("POST"))
        {
            $quantite = $request->get('quantite');
            $idProdstock = $request->get('id_cat');
            $user = $this->getUser();
            $id = $user->getId();
            $etat = 0;
            $user = $em->getRepository("UserBundle:Membre")->find($id);
            $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));

            if(empty($CMD1))
            {
                $CMD1 = new Commande();
                $CMD1->setIdUser($user);
                $etat = 0;
                $CMD1->setEtat($etat);
                $em->persist($CMD1);
                $em->flush();
            }

///////////////////////////////////////
            $prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);

            $Lignecommande = $em->getRepository('UserBundle:Panier')->findOneby(array("idStock" => $prodstock->getIdCat(),"idcommande"=>$CMD1->getIdCmd()));

            if (empty($Lignecommande))
            {
            if ($prodstock->getQuantitedispo() >= $quantite)
            {
                $ligneCMD = new Panier();
                $ligneCMD->setIdStock( $prodstock) ;
                $ligneCMD->setIdcommande($CMD1);
                $ligneCMD->setPrix($prodstock->getPrixuni());
                $ligneCMD->setQuantite($quantite);
                $prix = $prodstock->getPrixuni();
                $total = $prix * $quantite;
                /////////////////////
                $prodstock=$em->getRepository("UserBundle:Stock")->findOneBy(array("idCat"=>$ligneCMD->getIdStock()));
                /// ///////////
                $ligneCMD->setTotal($total);
                $totalcmd= $CMD1->getTotal();
                $CMD1->setTotal( $total+$totalcmd);
                $CMD1->setDate(new \DateTime());

                $quantiteproduit = $prodstock->getQuantitedispo();
                $qt = $quantiteproduit - $quantite;
                $prodstock->setQuantitedispo($qt);
                $em->persist($prodstock);
                $em->persist($ligneCMD);
                $em->persist($CMD1);
                $em->flush();
                $msg = 'Votre Produit est ajouté ';
                $msg1 = '';
                return $this->render('@User/Stock/list.html.twig', array(
                    'stock' =>  $stock, 'Message' => $msg, 'Message1' => $msg1 ));
            }
            else
            {
                $msg1 = "la quantite n'est plus disponible";
                $msg = '';
                return $this->render('@User/Stock/list.html.twig', array(
                    'stock' =>  $stock,  'Message' => $msg, 'Message1' => $msg1 ));
            }
            }
            else
            {
                $totalligne=$Lignecommande->getTotal();
                if ($prodstock->getQuantitedispo() >= $quantite)
                {


                    $quantite2= $Lignecommande->getQuantite();
                    $qte=$quantite+ $quantite2;
                    $Lignecommande->setQuantite( $qte);
                    $prix = $prodstock->getPrixuni();
                    $total = $prix * $qte;
                    //$totalanciene=$quantite* $prix;
                    /////////////////////
                    $prodstock=$em->getRepository("UserBundle:Stock")->findOneBy(array("idCat"=>$Lignecommande->getIdStock()));
                    /// ///////////
                    $Lignecommande->setTotal($total);

                    ///////////////////
                    $totalcmd= $CMD1->getTotal();
                    $totalcmdnew=$totalcmd-$totalligne;
                    $totalfinal=$total+ $totalcmdnew;

                    $CMD1->setTotal($totalfinal);
                    ////////////////////////////////
                    $CMD1->setDate(new \DateTime());

                    $quantiteproduit = $prodstock->getQuantitedispo();
                    $qt = $quantiteproduit - $quantite;
                    $prodstock->setQuantitedispo($qt);
                    $em->persist($CMD1);
                    $em->persist($prodstock);
                    $em->persist($Lignecommande);
                    $em->flush();
                    $msg = 'Votre Produit est ajouté ';
                    $msg1 = '';
                    return $this->render('@User/Stock/list.html.twig', array(
                        'stock' =>  $stock, 'Message' => $msg, 'Message1' => $msg1 ));
                }
                else
                {
                    $msg1 = "la quantité n'est plus disponible";
                    $msg = '';
                    return $this->render('@User/Stock/list.html.twig', array(
                        'stock' =>  $stock,  'Message' => $msg, 'Message1' => $msg1 ));
                }
            }


        }
        $msg = "";
        $msg1 = "";
        return $this->render('@User/Stock/list.html.twig', array(
            'stock' =>  $stock,  'Message' => $msg, 'Message1' => $msg1));



    }


 public function AffichePanierAction(Request $request)
 {
     $msg="";
     $msg1="";
     $em = $this->getDoctrine()->getManager();

     $id=$this->getUser()->getId();
     $etat = 0;
     $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $id, "etat" => $etat));

     $query=$em->createQuery("select p from UserBundle:Panier p join UserBundle:Commande c with c.idCmd =p.idcommande where c.idUser=:id and c.etat =0");
     $query->setParameter('id',$id);

     $panier= $query->getResult();

     return $this->render("@User/ClientEntreprise/panier.html.twig",array(
         'panier'=>$panier ,"Message" => $msg, "Message1" => $msg1
     ));
 }

    public function supprimercommandeAction(Request $request)
    {
        $user = $this->getUser();
        $id = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $etat = 0;
        $total="";
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $id, "etat" => $etat));

        $Lignecommande = $em->getRepository('UserBundle:Panier')->findBy(array("idcommande" => $CMD1->getIdCmd()));
        foreach ($Lignecommande as $x)
        {   $x= $em->getRepository('UserBundle:Panier')->find($x->getId());
            $idProdstock = $x->getIdStock();
            $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
            $quantite = $x->getQuantite();
            $quantiteproduit = $idProdstock->getQuantitedispo();
            $qt = $quantiteproduit + $quantite;
            $Prodstock->setQuantitedispo($qt);
            $em->persist($Prodstock);
            $em->remove($x);
            $em->flush();
        }
        $em->remove($CMD1);
        $em->flush();
        $lignecommande = $em->getRepository('UserBundle:Panier')->findby(array("idcommande" => $CMD1->getIdCmd()));
        $msg = 'vous avez supprimer la commande';
        $msg1='';
        return $this->render('@User/ClientEntreprise/panier.html.twig', array("panier" => $lignecommande, "Message" => $msg, "Message1" => $msg1,"Total"=>$total));
    }

    public function supprimerligneCommandeAction(Request $request)
    {
        $id=$request->get('id');
        $em= $this->getDoctrine()->getManager();
        /////ligne commande
        $ligneCommande=$em->getRepository('UserBundle:Panier')->find($id);
        //////////////////
        $user = $this->getUser();
        $idu = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $etat = 0;
        $idcmd=$ligneCommande->getIdcommande();
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu, "etat" => $etat ,"idCmd"=>$idcmd));
        ////////////////////////////////
        $totalcmd=$CMD1->getTotal();
        $totalligne=$ligneCommande->getTotal();
        $CMD1->setTotal($totalcmd-$totalligne);
        ////id de stock

        $idProdstock = $ligneCommande->getIdStock();
        ////// stock
        $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
        $quantite = $ligneCommande->getQuantite();
        $quantiteproduit = $Prodstock->getQuantitedispo(); //id
        $qt = $quantiteproduit + $quantite;
        $Prodstock->setQuantitedispo($qt);

        /////////commande////////////


        ///////////////////////
        $em->persist($Prodstock);
        $em->remove( $ligneCommande);
        $em->persist($CMD1);
        $em->flush();
        return $this->redirectToRoute("panier");
    }

    public function pdfAction(Request $request )
    {
        $snappy = $this->get('knp_snappy.pdf');
        $msg='';
        $em = $this->getDoctrine()->getManager();
        ////////
        $idcmd = $request->get('idCmd');
        $user = $this->getUser();
        $idu = $user->getId();
        $user = $em->getRepository("UserBundle:Membre")->find($idu);
        $etat = 1 ;
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu,"idCmd"=>$idcmd ,"etat"=>$etat));
     /////////////////////////////////
        $date=$CMD1->getDate();
        $str2 = $date->format("Y-m-d");
        $str2 = date('Y-m-d', strtotime($str2. ' + 10 days'));
        $dt = \DateTime::createFromFormat("Y-m-d", $str2);
     /////////////////////
        $query=$em->createQuery("select p from UserBundle:Panier p join UserBundle:Commande c with c.idCmd =p.idcommande where c.idUser=:id and c.etat = 1 and c.idCmd =:idCmd");
        $query->setParameters(array('id'=>$idu ,'idCmd'=>$idcmd));

        $panier= $query->getResult();
        //////////

        $html = $this->renderView('@User/ClientEntreprise/pdf2.html.twig', array("panier" => $panier, "Message" => $msg,'user'=>$user ,"date"=>$date ,"str2"=>$dt
            //..Send some data to your view if you need to //
        ));

        $filename = 'Facture';
        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }


    public function valideCommandeAction()
    {

        $msg2="Votre commande est réservée pendant 10 jours. Au-delà de ce délai, sans réception de paiement votre commande sera annulée. ";
        $msg3="Un rappel de paiement sera envoyée  par SMS  " ;
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $id = $user->getId();
        $etat = 0 ;
        $user = $em->getRepository("UserBundle:Membre")->find($id);
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));
        $date=$CMD1->getDate();
        $str2 = $date->format("Y-m-d");
        $str2 = date('Y-m-d', strtotime($str2. ' + 10 days'));
        $dt = \DateTime::createFromFormat("Y-m-d", $str2);
        $CMD1->setDateexp($dt);
        $CMD1->setEtat(1);
        $em->persist($CMD1);
        $em->flush();
        /////date de rappel///////////
        $str3 = $date->format("Y-m-d");
        $str3 = date('Y-m-d', strtotime($str3. ' + 5 days'));
        $dt2 = \DateTime::createFromFormat("Y-m-d", $str3);
//        if($dt2 ==  $datej=new  \ DateTime('now'))

if (1==1)
       {
            $basic  = new \Nexmo\Client\Credentials\Basic('a3dd8b6f', 'LEAnZuYO4KhtaVkO');
            $client = new \Nexmo\Client($basic);

           $message = $client->message()->send([
                'to' => '21620372189',
               'from' => 'Nexmo',
                'text' => 'Rappel de Paiement ! restez seulement 5 jours encore pour le paiement de votre facture '
           ]);
      }
        //supprimer commande

      else
      {
          $Lignecommande = $em->getRepository('UserBundle:Panier')->findOneby(array("idcommande" => $CMD1->getIdCmd()));


          foreach ($Lignecommande as $x)
          {   $x= $em->getRepository('UserBundle:Panier')->find($x->getId());
              $idProdstock = $x->getIdStock();
              $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
              $quantite = $x->getQuantite();
              $quantiteproduit = $Prodstock->getQuantitedispo();
              $qt = $quantiteproduit + $quantite;
              $Prodstock->setQuantitedispo($qt);
              $em->persist($Prodstock);
              $em->flush();
          }



      }





        return $this->render("@User/ClientEntreprise/validation.html.twig",array(
            'commande'=>$CMD1  ,'msg2'=>$msg2,'msg3'=>$msg3
        ));


    }

//public function SMSAction()
//
//{
//    $user = $this->getUser();
//    $id = $user->getId();
//    $em = $this->getDoctrine()->getManager();
//    $query=$em->createQuery("select c  from  UserBundle:Commande c  where c.idUser=:id and c.payement is NULL ");
//    $query->setParameter('id' ,$id);
//    $commandes= $query->getResult();
//
//    foreach ($commandes as $y)
//    {
//        $date=$y->getDate();
//        $str2 = $date->format("Y-m-d");
//        $str2 = date('Y-m-d', strtotime($str2. ' + 5 days'));
//        $dt = \DateTime::createFromFormat("Y-m-d", $str2);
////        if($dt ==  $datej=new  \ DateTime('yesterday'))
//        if(1==1)
//        {
//            $basic  = new \Nexmo\Client\Credentials\Basic('a3dd8b6f', 'LEAnZuYO4KhtaVkO');
//            $client = new \Nexmo\Client($basic);
//
//            $message = $client->message()->send([
//                'to' => '21620372189',
//                'from' => 'Nexmo',
//                'text' => 'Rappel de Paiement ! restez seulement 5 jours encore pour le paiement de votre facture de date'+$date+'consultez votre compte'
//            ]);
//        }
//        else
//        {
//            $Lignecommande = $em->getRepository('UserBundle:Panier')->findOneby(array("idcommande" => $y->getIdCmd()));
//
//
//            foreach ($Lignecommande as $x)
//            {   $x= $em->getRepository('UserBundle:Panier')->find($x->getId());
//                $idProdstock = $x->getIdStock();
//                $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
//                $quantite = $x->getQuantite();
//                $quantiteproduit = $Prodstock->getQuantitedispo();
//                $qt = $quantiteproduit + $quantite;
//                $Prodstock->setQuantitedispo($qt);
//                $em->persist($Prodstock);
//                $em->flush();
//            }
//
//
//
//
//
//
//        }
//        $this->redirectToRoute('CommandeClient');
//
//
//    }
//}


}
