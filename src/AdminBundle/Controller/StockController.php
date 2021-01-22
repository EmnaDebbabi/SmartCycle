<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 3/28/2019
 * Time: 8:14 PM
 */

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Stock;
use AdminBundle\Form\StockType;
use AdminBundle\Repository\StockRepository;



class StockController extends Controller
{


    public function ajoutAction(Request $request)
    {
        $count='';
        $stock=new Stock();
        $form=$this->createForm(StockType::class,$stock);
        $form->handleRequest($request);
        $desc=$stock->getDescription();
        $qtenew=$stock->getQuantitedispo();
        $em=$this->getDoctrine()->getManager();
        $stock2=$em->getRepository("UserBundle:Stock")->findOneBy(array('description'=>$desc));
        if($form->isSubmitted())
        {


           if (empty($stock2))
           {
               $stock->setQuantitedispo($request->get('quantitedispo'));
               $stock->setPrixuni($request->get('prixuni'));
               $stock->uploadProfilePicture();
               $em->persist($stock);
               $em->flush();

               return  $this->redirectToRoute('stock_list');
           }

           else
           {
               $qte = $stock2->getQuantitedispo();
               $qtenew=$request->get('quantitedispo');
               $stock2->setQuantitedispo($qte+$qtenew);
               $stock2->uploadProfilePicture();
               $em->persist($stock2);
               $em->flush();

               return  $this->redirectToRoute('stock_list');
           }


        }

        return $this->render('@Admin/StockAdmin/ajout.html.twig',array( "form"=>$form->createView(),'count'=>$count));
    }

    public function listStockAction()

    {
        $count='';
        $em=$this->getDoctrine()->getManager();
        $stock=$em->getRepository("UserBundle:Stock")->findAll();
        return $this->render("@Admin/StockAdmin/list.html.twig",array(
            'stock'=>$stock,'count'=>$count
        ));
    }

    public function DeleteStockAction(Request $request  )
    {

        $id=$request->get('id_cat');
        $em= $this->getDoctrine()->getManager();
        $stock=$em->getRepository('UserBundle:Stock')->find($id);
        $em->remove($stock);
        $em->flush();
        return $this->redirectToRoute("stock_list");
    }


    public function updateStockAction( Request $request)
    {
        $count='';
        $id=$request->get('id_cat');
        $em=$this->getDoctrine()->getManager();
        $stock=$em->getRepository("UserBundle:Stock")
            ->find($id);
        $stock->getQuantitedispo();
        $stock->getPrixuni();
        $form=$this->createForm(StockType::class,$stock);
        $form->handleRequest($request);
         $img=$stock->getNomimage();


        if ($form->isSubmitted())
        {
            $stock->setQuantitedispo($request->get('quantitedispo'));
            $stock->setPrixuni($request->get('prixuni'));
            $stock->uploadProfilePicture();
            $em->persist($stock);
            $em->flush();
            return $this->redirectToRoute("stock_list");

        }


        return $this->render('@Admin/StockAdmin/update.html.twig',array( "form"=>$form->createView() ,'stock'=>$stock ,"img"=>$img,'count'=>$count));


    }

    public function statAction()
    {
        $pieChart = new PieChart();
        $payement='paye';
        $em= $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT p as panier, p.quantite as qte ,s.description as description from UserBundle:Panier p  join UserBundle:Commande c   with p.idcommande=c.idCmd  join UserBundle:Stock s with p.idStock=s.idCat  where c.payement  = 'paye' group by p.idStock  "  );
        $stockpaye=$query->getResult();
        $data= array();
        $stat=[ 'qte','description'];
        $i=0;
        array_push($data,$stat);

        $ln= count($stockpaye);
        for ($i=0 ;$i<$ln;$i++){
            $stat=array();
            array_push($stat,$stockpaye[$i]['description'],$stockpaye[$i]['qte']/$ln);
            $stat=[$stockpaye[$i]['description'],$stockpaye[$i]['qte']*100/$ln];

            array_push($data,$stat);
        }
        $pieChart->getData()->setArrayToDataTable( $data );
        $pieChart->getOptions()->setTitle('Pourcentages des Stocks vendus');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
        return $this->render('@Admin/StockAdmin/statStockPaye.html.twig', array('piechart' => $pieChart));
    }





}