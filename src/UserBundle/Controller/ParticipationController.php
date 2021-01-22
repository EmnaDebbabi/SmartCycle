<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 3/29/2019
 * Time: 5:23 PM
 */

namespace UserBundle\Controller;


use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Evttesting;
use UserBundle\Entity\Participant;

class ParticipationController extends Controller
{
public function ParticiperAction(Request $request)
{
    ////
    $em= $this->getDoctrine()->getManager();
    $user=$this->getUser();
    $idu=$user->getId();

    $id=$request->get('id');

    $Event=$em->getRepository('UserBundle:Evttesting')->find($id);
    $participation=$em->getRepository('UserBundle:Participant')->findOneBy(array('idm'=>$idu,'idevent'=>$id));
//        $enseignes = $em->getRepository("ClientBundle:Participation")->findAll();

    $datej=new \DateTime('now');

    $week=date("Y-m-d", strtotime("-1 week"));

    $query = $em->createQuery('SELECT e FROM UserBundle:Evttesting e WHERE e.date >=:d1 ');
    $query->setParameter('d1',$week);

    $events = $query->getResult();

    if(empty($participation)){
        $msg='participant';
        $participation=new Participant();
        $participation->setEtat("interssé");
        $participation->setAvis("pas encore");
        $participation-> setIdM($user);
        $participation-> setIdevent($Event);
        $em->persist($participation);
        $em->persist($Event);
        $em->flush();
        $em=$this->getDoctrine()->getManager();
        $etat="interssé";
    $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);
    return $this->redirectToRoute('show_Event',array('MessageEvent'=>$msg,'events'=>$events));
    }

    $msg  =  'Déjà participant! Cliquez pour annuler!';
    $em=$this->getDoctrine()->getManager();
    $participation->setEtat("non interssé");
    $em->persist($Event);
    $em->flush();
    $etat="non interssé";
 $events=$em->getRepository(Evttesting::class)->findByTrierEventsUser($id);
    return $this->render('@User/Events/showEventt.html.twig',array('MessageEvent'=>$msg,'events'=>$events));
}
//////Statistiques
    public function statAction(){
        $pieChart = new PieChart();
        $em= $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p  ,UPPER(e.nom) as nom ,COUNT(p.idp) as num FROM UserBundle:Participant p 
join UserBundle:Evttesting e with e.id=p.idevent GROUP BY p.idevent');
        $participants=$query->getScalarResult();
        $data= array();
        $stat=['idevent', 'idp'];
        $i=0;
        array_push($data,$stat);

        $ln= count($participants);
        for ($i=0 ;$i<count($participants);$i++){
            $stat=array();
               array_push($stat,$participants[$i]['nom'],$participants[$i]['num']/$ln);
            $stat=[$participants[$i]['nom'],$participants[$i]['num']*100/$ln];

            array_push($data,$stat);
       }
        $pieChart->getData()->setArrayToDataTable( $data );
        $pieChart->getOptions()->setTitle('Pourcentages des participants par événement');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
        return $this->render('@User\Events\chartEvent.html.twig', array('piechart' => $pieChart));
    }

}