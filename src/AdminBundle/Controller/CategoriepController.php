<?php
/**
 * Created by PhpStorm.
 * User: goldzeo
 * Date: 16/04/2019
 * Time: 19:42
 */

namespace AdminBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\categorie;
use UserBundle\Entity\produit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategoriepController extends Controller
{
    /**
     *@Route("/", name="categorie_list")
     */
    public function listsAction(Request $request)
    {
        $em = $this->getDoctrine();
        $categorie = $em->getRepository('UserBundle:categorie')->findAll();
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result=$paginator->paginate(
            $categorie,
            $request->query->getInt('page',1),
            $request->query->getInt('limte',3)
        );
        return $this->render('@Admin/Recyclables/affichelistCategorie.html.twig', array(
            'categories' => $result,
        ));

        return $this->render('@Admin/Recyclables/lists:list.html.twig');
    }

    public function ajoutAction(Request $request){
        $categorie = new categorie();

        $form = $this->createForm('AdminBundle\Form\categorieType', $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $categorie->uploadProfilePicture();
            $em->persist($categorie);
            $em->flush();


            return $this->redirectToRoute('categorie_list'); }

        return $this->render('@Admin/Recyclables/ajoutcateg.html.twig', array(
            'produit' => $categorie,
            'form' => $form->createView(),
        ));
    }



    public function editAction(Request $request, categorie $categorie)
    {
        $editForm = $this->createForm('AdminBundle\Form\categorieType', $categorie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $categorie->uploadProfilePicture();

            $this->getDoctrine()->getManager()->persist($categorie);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('categorie_list');


        }



        return $this->render('@Admin/Recyclables/editcateg.html.twig', array(
            'categorie' => $categorie,
            'form_edit' => $editForm->createView(),

        ));

    }


    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $categorie = $em->getRepository('UserBundle:categorie')->find($id);

            $em->remove($categorie);
            $em->flush();

            return $this->redirectToRoute('categorie_list', array('id' => $categorie->getId()));

    }



    public function produitShowAction(Request $request)
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
        return $this->render('@Admin/Recyclables/AcceuilProduitBack.html.twig', array(
            'produits' => $result,
        ));

        return $this->render('@Admin/Recyclables/AcceuilProduitBack.html.twig');
    }

    public function deleteBackAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $produit = $em->getRepository('AdminBundle:produit')->find($id);



        if($produit->getStatus()=="en cour" || $produit->getStatus()=="refuser"){
            $em->remove($produit);
            $em->flush();

            return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
        }


        return $this->redirectToRoute('produit_show', array('id' => $produit->getId()));
    }


}