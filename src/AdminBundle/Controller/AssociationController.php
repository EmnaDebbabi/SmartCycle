<?php
/**
 * Created by PhpStorm.
 * User: med
 * Date: 04/04/2019
 * Time: 00:37
 */

namespace AdminBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Notification;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Swift_Message;


class AssociationController extends Controller
{
    public function listAction(){
        $em=$this->getDoctrine()->getManager();
        $demandes=$em->getRepository("UserBundle:Demande")->findAll();
        return $this->render("@Admin/Association/listDemandes.html.twig",array(
            'demande'=>$demandes
        ));
    }
    public function accepterAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository("UserBundle:Demande")->find($id);
        $demande->setEtat("valider");
        $em->persist($demande);
        $em->flush();
        // methode rapide pour creer des notifications
        $notif = new Notification();
        $notif->setIdm($em->getRepository("UserBundle:Membre")->find($demande->getIdm()));
        $notif
            ->setTitle('demande d\'adheration accepter')
            ->setDescription($this->getUser())
            ->setRoute('user_homepage')
            ->setParameters(array('ida'=> $id ));
        $em->persist($notif);
        $em->flush();

        $pusher = $this->get('mrad.pusher.notificaitons');
        $pusher->trigger($notif);
        return $this->redirectToRoute("demande_list");

    }

    public function refuserAction(Request $request,$id){
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository("UserBundle:Demande")->find($id);
        $demande->setEtat("refuser");
        $em->persist($demande);
        $em->flush();
        // methode rapide pour creer des notifications
        $notif = new Notification();
        $notif->setIdm($em->getRepository("UserBundle:Membre")->find($demande->getIdm()));
        $notif
            ->setTitle('demande d\'adheration refuser')
            ->setDescription($this->getUser())
            ->setRoute('user_homepage')
            ->setParameters(array('ida'=> $id ));
        $em->persist($notif);
        $em->flush();

        $pusher = $this->get('mrad.pusher.notificaitons');
        $pusher->trigger($notif);

        return $this->redirectToRoute("demande_list");

    }

    public function statsAction(){
        $pieChart = new PieChart();
        $em = $this->getDoctrine()->getManager();
        $asso = $em->getRepository("UserBundle:Demande")->findAdherationStats();
        $data= array();
        $stat=['idevent', 'idp'];
        $i=0;
        array_push($data,$stat);

        $ln= count($asso);
        for ($i=0 ;$i<count($asso);$i++){
            $stat=array();
            array_push($stat,$asso[$i]['nom'],$asso[$i]['num']/$ln);
            $stat=[$asso[$i]['nom'],$asso[$i]['num']*100/$ln];

            array_push($data,$stat);
        }
        $pieChart->getData()->setArrayToDataTable( $data );
        $pieChart->getOptions()->setTitle('Pourcentages des adhérations par association');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render("@Admin/Association/stats.html.twig", array('piechart' => $pieChart));
    }

    public function listAvisAction(){
        $em=$this->getDoctrine()->getManager();
        $demandes=$em->getRepository("UserBundle:Rating")->findAll();
        return $this->render("@Admin/Association/listAvis.html.twig",array(
            'demande'=>$demandes
        ));

    }

    public function mailAction(Request $request){
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $rate = $em->getRepository("UserBundle:Rating")->find($id);


//        if($request->isMethod('POST')){
            $message = \Swift_Message::newInstance()
                ->setSubject($request->get('subj'))
                ->setFrom('SmartCycle@esprit.tn','yoo')
                ->setTo('mobh@live.fr');

            $this->get('mailer')->send($message);

//        }


        return $this->render("@Admin/Association/mailpage.html.twig",array(
            'rate'=>$rate
        ));

    }


}