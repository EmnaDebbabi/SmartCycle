<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 4/27/2019
 * Time: 2:18 PM
 */

namespace PiMobileBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use UserBundle\Entity\Commande;
use UserBundle\Entity\Panier;
use UserBundle\Entity\Stock;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;



class PanierController extends Controller
{
    public function ajouterlcAction($id,$idstock,$qt)
    {
        $em = $this->getDoctrine()->getManager();
        $etat = 0;
        $user = $em->getRepository("UserBundle:Membre")->find($id);
        $prodstock = $em->getRepository("UserBundle:Stock")->find($idstock);
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));

        if(empty($CMD1 ))
        {
            $CMD1 = new Commande();
            $CMD1->setIdUser($user);
            $etat = 0;
            $CMD1->setEtat($etat);
            $em->persist($CMD1);
            $em->flush();
        }
        $Lignecommande = $em->getRepository('UserBundle:Panier')->findOneby(array("idStock" => $prodstock->getIdCat(),"idcommande"=>$CMD1->getIdCmd()));        if (empty($Lignecommande))
        {
            $ligneCMD = new Panier();
            $ligneCMD->setIdStock($prodstock);
            $ligneCMD->setIdcommande($CMD1);
            $ligneCMD->setPrix($prodstock->getPrixuni());
            $ligneCMD->setQuantite($qt);
            $ligneCMD->setTotal($prodstock->getPrixuni() * $qt);


            $CMD1->setTotal($CMD1->getTotal()+$prodstock->getPrixuni() * $qt);
            $CMD1->setDate(new \DateTime());
            $prodstock->setQuantitedispo($prodstock->getQuantitedispo()-$qt);
            $em->persist($prodstock);
            $em->persist($ligneCMD);
            $em->persist($CMD1);
            $em->flush();
        }

         else
         {

                 $quantite2= $Lignecommande->getQuantite();
                 $Lignecommande->setQuantite( $qt +$quantite2);
                 $prix = $prodstock->getPrixuni();
                 $prodstock=$em->getRepository("UserBundle:Stock")->findOneBy(array("idCat"=>$Lignecommande->getIdStock()));
                 $Lignecommande->setTotal(($prix* $qt) +$Lignecommande->getTotal());
                 $totalcmd= $CMD1->getTotal();
                 $totalcmdnew=$totalcmd-($prix*$quantite2);
                 $totalfinal=$prix*( $qt +$quantite2)+ $totalcmdnew;

                 $CMD1->setTotal($totalfinal);
                 ////////////////////////////////
                 $CMD1->setDate(new \DateTime());
                 $prodstock->setQuantitedispo($prodstock->getQuantitedispo()-$qt);
                 $em->persist($CMD1);
                 $em->persist($prodstock);
                 $em->persist($Lignecommande);
                 $em->flush();
         }

        return new JsonResponse();
    }
    public function AllLcAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $query=$em->createQuery("select p.id , p.quantite ,p.prix ,p.total ,s.description ,s.idCat ,s.nomimage , s.quantitedispo,c.idCmd from UserBundle:Panier p join UserBundle:Commande c with c.idCmd =p.idcommande  join UserBundle:Stock s with p.idStock=s.idCat  where c.idUser=:id and c.etat =0");
        $query->setParameter('id',$id);

        $panier= $query->getResult();

        $serializer=new  Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($panier);
        return new JsonResponse($formatted);
    }
    public function supprimerLComAction($idu,$id)
    {   $em = $this->getDoctrine()->getManager();
        $ligneCommande = $em->getRepository('UserBundle:Panier')->find($id);
        $em = $this->getDoctrine()->getManager();
        $etat = 0;
        $idcmd=$ligneCommande->getIdcommande();
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu, "etat" => $etat ,"idCmd"=>$idcmd));
        $totalcmd=$CMD1->getTotal();
        $totalligne=$ligneCommande->getTotal();
        $CMD1->setTotal($totalcmd-$totalligne);
        $idProdstock = $ligneCommande->getIdStock();
        $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
        $quantite = $ligneCommande->getQuantite();
        $quantiteproduit = $Prodstock->getQuantitedispo(); //id
        $qt = $quantiteproduit + $quantite;
        $Prodstock->setQuantitedispo($qt);
        $em->persist($Prodstock);
        $em->remove( $ligneCommande);
        $em->persist($CMD1);
        $em->flush();

        return new JsonResponse();
    }
    public function supprimerComAction($id)
    {   $em = $this->getDoctrine()->getManager();
        $etat = 0;
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $id,"etat" => $etat));
        $Lignecommande = $em->getRepository('UserBundle:Panier')->findBy(array("idcommande" => $CMD1->getIdCmd()));
        foreach ($Lignecommande as $x)
        {

            $x= $em->getRepository('UserBundle:Panier')->find($x->getId());
            $idProdstock = $x->getIdStock();
            $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
            $quantite = $x->getQuantite();
            $quantiteproduit = $Prodstock->getQuantitedispo();
            $qt = $quantiteproduit + $quantite;
            $Prodstock->setQuantitedispo($qt);
            $em->persist($Prodstock);
            $em->remove($x);

            $em->flush();
        }

        $em->remove($CMD1);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($CMD1);
        return new JsonResponse($formatted);
    }

    public function modifqtAction($id,$qt)
    {


        $em = $this->getDoctrine()->getManager();
        $etat = 0 ;
        $Lignecommande = $em->getRepository('UserBundle:Panier')->find($id);
        $idcmd = $Lignecommande->getIdcommande();
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array( "etat" => $etat ,"idCmd"=>$idcmd));
        $totalcmd=$CMD1->getTotal();

        $idProdstock = $Lignecommande->getIdStock();
        $Prodstock = $em->getRepository("UserBundle:Stock")->find($idProdstock);
        $quantiteproduit = $Prodstock->getQuantitedispo();
        $qt2 = ($quantiteproduit + $Lignecommande->getQuantite() )-$qt;
        $Prodstock->setQuantitedispo($qt2);
        $em->persist($Prodstock);



        $totalligne=$Lignecommande->getTotal();
        $totalcmd2= $totalcmd-  $totalligne;

        $Lignecommande->setQuantite($qt);
        $prix = $Lignecommande->getPrix();
        $total = $prix *$qt;
        $CMD1->setTotal($totalcmd2+$total);
        $Lignecommande->setTotal($total);






        $em->persist($Lignecommande);
        $em->persist($CMD1);
        $em->flush();


        return new JsonResponse();
    }

    public function validerAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $etat = 0 ;

        $user = $em->getRepository("UserBundle:Membre")->find($id);
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $user, "etat" => $etat));
//        $CMD1->getDate($request->get('date'));

        $date = new  \DateTime('now');
//
        $CMD1->setDate(   $date );
        $str2 = $date->format("Y-m-d");
        $str2 = date('Y-m-d', strtotime($str2. ' + 10 days'));
        $dt = \DateTime::createFromFormat("Y-m-d", $str2);
        $CMD1->setDateexp($dt);
        $CMD1->setEtat(1);
        $CMD1->setPayement("non paye");
        $em->persist($CMD1);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($CMD1);
        return new JsonResponse($formatted);
    }

    public function mesCommandesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $query=$em->createQuery(" SELECT e.idCmd ,e.date ,e.dateexp ,e.total,e.payement FROM UserBundle:Commande e WHERE e.idUser=:id and e.etat = 1");
        $query->setParameter('id',$id);
        $CMD1= $query->getResult();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($CMD1);
        return new JsonResponse($formatted);
    }

    public function pointAction($id)
{
        $pointcal='calcule';
         $em = $this->getDoctrine()->getManager();

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

    $info = array(
        'info' => $point2
    );
    $list =array($info);
    $serializer=new Serializer([new ObjectNormalizer()]);
    $formatted=$serializer->normalize($list);
    return new JsonResponse($formatted);
}



    public function accederaupointAction($id)
    {
        $etat=0;
        $pointcal='calcule';
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:Membre")->find($id);
        ///////////////////////////////////////
        $query=$em->createQuery("select c ,Sum(c.total) as sss from  UserBundle:Commande c  where c.idUser=:id and c.payement = :paye and c.point = :calcule ");
        $query->setParameters(array('id'=>$id,'paye'=>"paye",'calcule'=>$pointcal));
        $commandes= $query->getResult();
        $sum = $commandes[0]['sss'];
        $point = $sum/10 ;
        /// ////////////////////////
        $commandes2=$em->getRepository("UserBundle:Commande")->findBy(array("idUser" => $id, "point" => $pointcal));

        ///////////////////////////////////////////////////
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $id, "etat" => $etat));
        $random=random_int(1,4);
        $stock=$em->getRepository("UserBundle:Stock")->find($random);


        $kilo=round($point/100)*5;



        if ($point >= 100)
        {
            if(empty($CMD1))
            {
                $CMD1 = new Commande();
                $CMD1->setIdUser($user);
                $etat = 0;
                $CMD1->setEtat($etat);
                $em->persist($CMD1);
                $em->flush();
            }

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
            $stock->setQuantitedispo($qterestant);
            $em->persist($stock);
            $em->flush();
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


        }

        $info = array(
            'info' => $point
        );
        $list =array($info);
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($list);

        return new JsonResponse($formatted);







    }
    public function paiementAction($idu ,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $etat = 1;
        $CMD1 = $em->getRepository("UserBundle:Commande")->findOneBy(array("idUser" => $idu, "idCmd" => $id, "etat" => $etat));
        $CMD1->setPayement("paye");
        $em->persist($CMD1);
        $em->flush();
        return new JsonResponse();
    }


}