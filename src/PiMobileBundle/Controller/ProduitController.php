<?php
/**
 * Created by PhpStorm.
 * User: brini
 * Date: 5/22/2019
 * Time: 1:39 PM
 */

namespace PiMobileBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\categorie;
use UserBundle\Entity\produit;


class ProduitController extends Controller
{
    public function newAction(Request $request)
    {
        $status='nv';

        $produit = new produit();
        $produit->setStatus($status);
        $categorie= new categorie();
        $categorie->setType($request->get('typeproduit'));
        $em = $this->getDoctrine()->getManager();
        $produit->setNomproduit($request->get('nomproduit'));
        $produit->setTypeproduit($categorie);
        $produit->setQuantiteproduit($request->get('quantiteproduit'));
        $produit->setDescription($request->get('description'));
        $produit->setNomImage($request->get('nomImage'));
        $em->persist($produit);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produit);
        return new JsonResponse($formatted);

    }
    public function affAction()
    {
        $em = $this->getDoctrine();
        $produits = $em->getRepository('UserBundle:produit')->findAll();


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($produits);
        return new JsonResponse($formatted);

    }
}