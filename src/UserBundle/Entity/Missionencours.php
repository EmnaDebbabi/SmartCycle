<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Missionencours
 *
 * @ORM\Table(name="missionencours", indexes={@ORM\Index(name="id_transporteur", columns={"id_transporteur"}), @ORM\Index(name="id_produit", columns={"id_produit"})})
 * @ORM\Entity
 */
class Missionencours
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_transporteur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTransporteur;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_produit", type="integer", nullable=false)
     */
    private $idProduit;

    /**
     * @return int
     */
    public function getIdTransporteur()
    {
        return $this->idTransporteur;
    }

    /**
     * @param int $idTransporteur
     */
    public function setIdTransporteur($idTransporteur)
    {
        $this->idTransporteur = $idTransporteur;
    }

    /**
     * @return int
     */
    public function getIdProduit()
    {
        return $this->idProduit;
    }

    /**
     * @param int $idProduit
     */
    public function setIdProduit($idProduit)
    {
        $this->idProduit = $idProduit;
    }


}

