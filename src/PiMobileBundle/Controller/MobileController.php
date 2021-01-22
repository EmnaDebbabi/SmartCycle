<?php

namespace TransportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Missionencours;
use UserBundle\Entity\produit;
use UserBundle\Entity\transporteur;
use Symfony\Component\HttpFoundation\Response;

class MobileController extends Controller
{
    public function indexAction(Request $request)
    {
        $requestCIN = $request->get('cin');

        $em = $this->getDoctrine()->getManager();
        $transporteur = $em->getRepository('TransportBundle:transporteur')->findOneBy([ 'cin' => $requestCIN ]);

        if(is_null($transporteur)){
            return new Response(json_encode("NotExist"));
        }else{
            $result['id'] = $transporteur->getId();
            $result['nom'] = $transporteur->getNom();
            $result['prenom'] = $transporteur->getPrenom();
            $result['cin'] = $transporteur->getCin();
            $result['adresse'] = $transporteur->getAdresse();
            $result['telephone'] = $transporteur->getTelephone();
            $result['permit'] = $transporteur->getPermit();
            return new Response(json_encode($result));
        }
    }

    public function produitAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $produits = $this->getDoctrine()->getRepository('TransportBundle:produit')->findAll();


        foreach ($produits as $entity) {
            $response[] = array(
                'id' => $entity->getId(),
                'nom' => $entity->getNomproduit(),
                'descriptipn' =>$entity->getDescription(),
                'type' => $entity->getTypeproduit(),
                'quantite' => $entity->getQuantiteproduit(),

                'image' => $entity->getImage(),
                'status' => $entity->getStatus(),
                'dat' => $entity->getDat());
        }

        return new Response(json_encode($response));

    }

    public function produitAction($id)
    {

        $entity = $this->getDoctrine()->getRepository('TransportBundle:produit')->find($id);

        $response['id'] =$entity->getId();
        $response['nom'] = $entity->getNomproduit();
        $response['descriptipn'] =$entity->getDescription();
        $response['type'] = $entity->getTypeproduit();
        $response['quantite'] = $entity->getQuantiteproduit();

        $response['image'] =$entity->getImage();
        $response['status'] =$entity->getStatus();
        $response['dat'] = $entity->getDat();


        return new Response(json_encode($response));

    }


    public function missionAllAction($id)
    {
        $missions = $this->getDoctrine()->getRepository('TransportBundle:Missionencours')->findBy([ 'idt' => $id ]);
        foreach ($missions as $entity) {
            $transporteur = $this->getDoctrine()->getRepository('TransportBundle:transporteur')->find(intval($entity->getIdt()));
            $produit = $this->getDoctrine()->getRepository('TransportBundle:produit')->find(intval($entity->getIdpr()));
            $response[] = array(
                'id' => $entity->getIdm(),
                'idprod' => $entity->getIdpr(),
                'idtrans' =>$entity->getIdt(),
                'nomtrans' => $transporteur->getNom()." ".$transporteur->getPrenom(),
                'qte' => $produit->getQuantiteproduit(),
                'nomprod' => $produit->getNomproduit());
        }

        try{
            return new Response(json_encode($response));
        }
        catch(\Exception $e){
            return new Response(json_encode('NAN'));
        }

    }


    public function missionAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('TransportBundle:Messionencours')->find($id);
        $transporteur = $this->getDoctrine()->getRepository('TransportBundle:transporteur')->find(intval($entity->getIdt()));
        $produit = $this->getDoctrine()->getRepository('TransportBundle:produit')->find(intval($entity->getIdpr()));
        $response['id'] = $entity->getIdm();
        $response['idprod'] = $entity->getIdpr();
        $response['idtrans'] = $entity->getIdt();
        $response[ 'nomtrans'] = $transporteur->getNom()." ".$transporteur->getPrenom();
        $response['nomprod'] = $produit->getNomproduit();
        return new Response(json_encode($response));
    }


    public function ajouteMisAction(Request $request)
    {
        $requestIDTrans = $request->get('id');
        $requestIDProd = $request->get('prod');

        $produit = $this->getDoctrine()->getRepository('TransportBundle:produit')->find(intval($requestIDProd));
        $produit->setStatus('v');

        $mission = new Missionencours();
        $mission->setIdt(intval($requestIDTrans));
        $mission->setIdpr(intval($requestIDProd));

        $em = $this->getDoctrine()->getManager();
        $em->persist($mission);
        $em->flush($mission);
        $em->flush($produit);
        return new Response(json_encode('DONE'));
    }


    public function deleteMisAction($id)
    {
        $mission = $this->getDoctrine()->getRepository('TransportBundle:Messionencours')->find($id);
        $produit = $this->getDoctrine()->getRepository('TransportBundle:produit')->find(intval($mission->getIdpr()));
        $produit->setStatus('nv');
        $em = $this->getDoctrine()->getManager();
        $em->remove($mission);
        $em->flush($mission);
        $em->flush($produit);
        return new Response(json_encode('DONE'));
    }



}
