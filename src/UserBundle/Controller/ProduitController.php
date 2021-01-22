<?php

namespace UserBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitController extends Controller
{
    /**
     *@Route("/", name="produit_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine();
        $produits = $em->getRepository('UserBundle:produit')->findAll();
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result=$paginator->paginate(
            $produits,
            $request->query->getInt('page',1),
            $request->query->getInt('limte',3)
        );
        return $this->render('@User/Recyclables/AcceuilProduit.html.twig', array(
            'produits' => $result,
        ));
        return $this->render('@User/Recyclables/tous:index.html.twig');

    }
    public function affAction()
    {
        $em = $this->getDoctrine();
        $produits = $em->getRepository('UserBundle:produit')->findAll();


        return $this->render('@User/Recyclables/afficheproduits.html.twig', array(
            'produits' => $produits,
        ));

//        return $this->render('ProduitBundle:base2:aff.html.twig');
    }
    public function newAction(Request $request)
    {
        $produit = new produit();
        $status='nv';
        $form = $this->createForm('UserBundle\Form\produitType', $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $produit->uploadProfilePicture();
            $produit->setStatus($status);
            $datej=new  \ DateTime('now');
            $produit->setDat($datej);
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('produit_index'); }

        return $this->render('@User/Recyclables/Ajouter.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, produit $produit)
    {
        $editForm = $this->createForm('UserBundle\Form\produitType', $produit);
        $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $produit->uploadProfilePicture();
                $this->getDoctrine()->getManager()->persist($produit);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('produit_index');


            }



        return $this->render('@User/Recyclables/edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),

        ));

    }

    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('UserBundle:produit')->find($id);



        if($produit->getStatus()=="en cour" || $produit->getStatus()=="refuser"){
            $em->remove($produit);
            $em->flush();

            return $this->redirectToRoute('produit_index', array('id' => $produit->getId()));
        }


        return $this->redirectToRoute('produit_index', array('id' => $produit->getId()));
    }
}
