<?php
/**
 * Created by PhpStorm.
 * User: AHmeD
 * Date: 09/04/2019
 * Time: 16:39
 */

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\produit;
use UserBundle\Entity\transporteur;
use UserBundle\UserBundle;

class MessionsController extends Controller
{
    /**
     * @return mixed
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();

        $produits = $em->getRepository('UserBundle:produit')->produitsvalide('nv');


        return $this->render('@Admin/Default/accuileMession.html.twig', array(
            'produits' => $produits,
        ));


        return $this->render('@Admin/Default/accuileMession.html.twig');

    }


    public function affecteeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $tranports = $em->getRepository('UserBundle:transporteur')->findAll();
settype($id,'integer');
        $p=$em->getRepository('UserBundle:produit')->find($id);
       // var_dump($p);
        $p->setStatus('v');

        $em->flush();

        return $this->render('@Admin/Default/affecttrans.html.twig', array(
            'transports' => $tranports,
        ));


        return $this->render('@Admin/Default/accuileMession.html.twig');

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


}