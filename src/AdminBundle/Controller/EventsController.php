<?php
/**
 * Created by PhpStorm.
 * User: debba
 * Date: 4/4/2019
 * Time: 1:28 PM
 */

namespace AdminBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\EvttestingType;
use UserBundle\Entity\Evttesting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use UserBundle\Entity\Participant;
class EventsController extends Controller
{
    /////Show all event
    public function showEventAction(Request $request)
    {
        $test=0;
        $em=$this->getDoctrine()->getManager();
        $datej=  new \DateTime('now');
        $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        return $this->render("@Admin/Events/showEvent.html.twig",array(
            'events'=>$events,'datej'=>$datej,'test'=>$test
        ));
    }
    ///Remove any Event
    public function RemoveEventAction(Request $request)
    {
        $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $evttesting=$em->getRepository("UserBundle:Evttesting")->find($id);
        $em->remove($evttesting);
        $em->flush();
        return $this->redirectToRoute("Show_Event");
    }

    //Update any Event
    public function UpdateEventAction(Request $request){
        $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $evttesting=$em->getRepository("UserBundle:Evttesting")->find($id);
        $img=$evttesting->getNomImage();
        $nom=$evttesting->getNom();
        $date=$evttesting->getDate();
        $fot = $date->format('Y-m-d');
        $form=$this->createForm(EvttestingType::class,$evttesting);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $sdt =$request->get('date');
            $dt = DateTime::createFromFormat("Y-m-d", $sdt);
            $evttesting->setDate($dt);
            $evttesting->setIdm($this->getUser());
            $evttesting->uploadProfilePicture();
            $em->persist($evttesting);
            $em->flush();
            return $this->redirectToRoute("Show_Event");
        }
        return $this->render("@Admin/Events/updateEvent.html.twig",array(
            'form'=>$form->createView(),'evttesting'=>$evttesting ,"img"=>$img,"nom"=>$nom,"fot"=>$fot));
    }
    /////Show Avis
    public function showAvisAction()
    {
        $em=$this->getDoctrine()->getManager();
        $avis=$em->getRepository("UserBundle:Participant")->findAll();
        return $this->render("@Admin/Events/showAvis.html.twig",array(
            'avis'=>$avis
        ));
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
        return $this->render('@Admin\Events\chartEvent.html.twig', array('piechart' => $pieChart));
    }
    /////////Rechercher Events par nom
    public function TrierParNomAction(Request $request)
    {
        $test=1;
        $em=$this->getDoctrine()->getManager();
        $datej=  new \DateTime('now');
        $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        return $this->render("@Admin/Events/showEvent.html.twig",array(
            'events'=>$events,'datej'=>$datej,'test'=>$test
        ));
    }
    /////////Rechercher Events partype
    public function TrierParTypeAction(Request $request)
    {
        $test=2;
        $em=$this->getDoctrine()->getManager();
        $datej=  new \DateTime('now');
        $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        return $this->render("@Admin/Events/showEvent.html.twig",array(
            'events'=>$events,'datej'=>$datej,'test'=>$test
        ));
    }
    /////////Rechercher Events parLieu
    public function TrierParLieuAction(Request $request)
    {
        $test=3;
        $em=$this->getDoctrine()->getManager();
        $datej=  new \DateTime('now');
        $events=$em->getRepository("UserBundle:Evttesting")->findAll();
        return $this->render("@Admin/Events/showEvent.html.twig",array(
            'events'=>$events,'datej'=>$datej,'test'=>$test
        ));
    }
}
