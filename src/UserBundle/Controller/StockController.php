<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 3/30/2019
 * Time: 12:34 PM
 */

namespace UserBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Stock;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



class StockController extends  Controller
{

    public function listStockAction(Request $request )
    {
        $msg ="";
        $msg1="";
        $em=$this->getDoctrine()->getManager();
        $query=$em->createQuery(" SELECT e FROM UserBundle:Stock e WHERE e.quantitedispo > 0 " );


        $stock= $query->getResult();


        return $this->render("@User/Stock/list.html.twig",array(

            'Message' => $msg,'Message1' => $msg1,'stock'=>$stock
        ));
    }
    public function rechercherStockAction(Request $request)
    {   $msg ="";
        $msg1="";

        $criteria=$request->get('search');
        $em=$this->getDoctrine()->getManager();
        $stock=$em->getRepository("UserBundle:Stock")->findBy(array('description'=>$criteria));

        return $this->render("@User/Stock/list.html.twig",array(  'Message' => $msg,'Message1' => $msg1 ,'stock'=>$stock));
    }

    public function SearchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $posts =  $em->getRepository('UserBundle:Stock')->findEntitiesByString($requestString);
        if(!$posts) {
            $result['posts']['error'] = "Post Not found :( ";
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($posts)
    {
        foreach ($posts as $posts){
            $realEntities[$posts->getIdCat()] = [$posts->getNomimage(),$posts->getQuantitedispo(),$posts->getDescription(),$posts->getPrixuni(),$posts->getIdCat()];
        }
        return $realEntities;
    }


}
