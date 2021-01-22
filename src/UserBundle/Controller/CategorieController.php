<?php
/**
 * Created by PhpStorm.
 * User: goldzeo
 * Date: 16/04/2019
 * Time: 19:42
 */

namespace ProduitBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\categorie;
use UserBundle\Entity\produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategorieController extends Controller
{
    /**
     *@Route("/", name="categorie_list")
     */
    public function listsAction(Request $request)
    {
        $em = $this->getDoctrine();
        $categorie = $em->getRepository('ProduitBundle:categorie')->findAll();
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result=$paginator->paginate(
            $categorie,
            $request->query->getInt('page',1),
            $request->query->getInt('limte',3)
        );
        return $this->render('@Produit/adminviews/affichelistCategorie.html.twig', array(
            'categories' => $result,
        ));

        return $this->render('@Produit/adminviews/lists:list.html.twig');
    }

    public function ajoutAction(Request $request){
        $categorie = new categorie();

        $form = $this->createForm('ProduitBundle\Form\categorieType', $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            $file = $categorie->getImagecategorie();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('photos_directory'), $fileName);
            $categorie->setImagecategorie($fileName);

            return $this->redirectToRoute('categorie_list'); }

        return $this->render('@Produit/adminviews/ajoutcateg.html.twig', array(
            'produit' => $categorie,
            'form' => $form->createView(),
        ));
    }



    public function editAction(Request $request, categorie $categorie)
    {
        $editForm = $this->createForm('ProduitBundle\Form\categorieType', $categorie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_list');


        }



        return $this->render('@Produit/adminviews/editcateg.html.twig', array(
            'categorie' => $categorie,
            'form_edit' => $editForm->createView(),

        ));

    }


    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository('ProduitBundle:categorie')->find($id);




            $em->remove($categorie);
            $em->flush();

            return $this->redirectToRoute('categorie_list', array('id' => $categorie->getId()));



        return $this->redirectToRoute('categorie_list', array('id' => $categorie->getId()));
    }



    public function produitShowAction(Request $request)
    {
        $em = $this->getDoctrine();
        $produits = $em->getRepository('ProduitBundle:produit')->findAll();
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result=$paginator->paginate(
            $produits,
            $request->query->getInt('page',1),
            $request->query->getInt('limte',3)
        );
        return $this->render('@Produit/adminviews/AcceuilProduitBack.html.twig', array(
            'produits' => $result,
        ));

        return $this->render('@Produit/adminviews/AcceuilProduitBack.html.twig');
    }

    public function deleteBackAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('ProduitBundle:produit')->find($id);



        if($produit->getStatus()=="en cour" || $produit->getStatus()=="refuser"){
            $em->remove($produit);
            $em->flush();

            return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
        }


        return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
    }


}