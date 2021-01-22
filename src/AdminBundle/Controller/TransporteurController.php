<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\transporteur;

class TransporteurController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine();

        $tranports = $em->getRepository('UserBundle:transporteur')->findAll();

        return $this->render('@Admin/Default/AcceuilTransporteur.html.twig', array(
            'transports' => $tranports,
        ));


        return $this->render('Default/index.html.twig');

    }

    public function  showAction($id)
    {

        $em = $this->getDoctrine();
        $transports = $em->getManager()->getRepository('UserBundle:transporteur')->find($id);

        return $this->render('@Admin/Default/AcceuilTransporteur.html.twig', array(
            'transports' => $transports,

        ));
    }

        public function newAction(Request $request ){



            $transporteur = new transporteur();



            $form = $this->createForm('AdminBundle\Form\transporteurType', $transporteur);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->persist($transporteur);
                $em->flush();

                return $this->redirectToRoute('transporteur_index'); }

                return $this->render('@Admin/Default/Ajouter.html.twig', array(
                    'transporteur' => $transporteur,
                    'form' => $form->createView(),
                ));
            }



    public function editAction(Request $request, transporteur $transporteur)
    {
        $editForm = $this->createForm('AdminBundle\Form\transporteurType', $transporteur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transporteur_index');


        }

        return $this->render('@Admin/Default/eedit.html.twig', array(
            'transporteur' => $transporteur,
            'edit_form' => $editForm->createView(),

        ));

    }


    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $transporteur = $em->getRepository('UserBundle:transporteur')->find($id);
        $em->remove($transporteur);
        $em->flush();

        return $this->redirectToRoute('transporteur_index', array('id' => $transporteur->getId()));
    }

}