<?php
/**
 * Created by PhpStorm.
 * User: jalel
 * Date: 04/04/2019
 * Time: 16:03
 */

namespace AdminBundle\Controller;


use AdminBundle\Form\CategorietopicsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AdminBundle\Repository\FroumRepository;
use UserBundle\Entity\Categorietopics;

use Symfony\Component\HttpFoundation\Request;

class categorieController extends Controller
{
 public function addCatAction (Request $request)
 {
     $cat =new Categorietopics();
     $form=$this->createForm(CategorietopicsType::class,$cat);
     $form->handleRequest($request);

     if($form->isSubmitted())
     {
         $em=$this->getDoctrine()->getManager();


         $em->persist($cat);
         $em->flush();
         return $this->redirectToRoute("listerCategorie");

     }

     return $this->render('@Admin/Forum/ajouterCategorie.html.twig',array( "form"=>$form->createView()));
 }

    public function updateCatAction( Request $request)
    {
        $em=$this->getDoctrine()->getManager();
       $id=$request->get('catId');

        $cat=$em->getRepository("UserBundle:Categorietopics")
            ->find($id);

        $form=$this->createForm(CategorietopicsType::class,$cat);
        $form->handleRequest($request);


        if ($form->isSubmitted())
        {
            $em->persist($cat);
            $em->flush();
            return $this->redirectToRoute("listerCategorie");

        }


        return $this->render('@Admin/Forum/modifierCategorie.html.twig',array( "form"=>$form->createView() ,'Categorietopics'=>$cat));


    }

    public function listCatAction()
    {
        $em=$this->getDoctrine()->getManager();
        $cat=$em->getRepository("UserBundle:Categorietopics")->findAll();
        return $this->render("@Admin/Forum/listeCategorie.html.twig",array(
            'Categorietopics'=>$cat
        ));
    }

    public function DeleteCatAction(Request $request  )
    {
        $id=$request->get('catId');
        $em= $this->getDoctrine()->getManager();
        $cat=$em->getRepository('UserBundle:Categorietopics')->find($id);
        $em->remove($cat);
        $em->flush();
        /*return $this->render("@Admin/Forum/listeCategorie.html.twig",array(
            'Categorietopics'=>$cat
        ));*/
        return $this->redirectToRoute("listerCategorie");

    }


}