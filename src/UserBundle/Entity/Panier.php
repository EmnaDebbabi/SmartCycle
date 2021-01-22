<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Panier
 *
 * @ORM\Table(name="panier", indexes={@ORM\Index(name="IDX_24CC0DF29D333A79", columns={"idstock"}), @ORM\Index(name="IDX_24CC0DF2C43FEE70", columns={"idcommande"})})
 * @ORM\Entity
 */
class Panier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=true)
     */
    private $prix;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=true)
     */
    private $total;

    /**
     * @var \Stock
     *
     * @ORM\ManyToOne(targetEntity="Stock",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idstock", referencedColumnName="id_cat", onDelete="CASCADE")
     * })
     */
    private $idStock;

    /**
     * @var \Commande
     *
     * @ORM\ManyToOne(targetEntity="Commande",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcommande", referencedColumnName="id_cmd", onDelete="CASCADE")
     * })
     */
    private $idcommande;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * @param int $quantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    /**
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return Stock
     */
    public function getIdStock()
    {
        return $this->idStock;
    }

    /**
     * @param Stock $idStock
     */
    public function setIdStock($idStock)
    {
        $this->idStock = $idStock;
    }

    /**
     * @return \Commande
     */
    public function getIdcommande()
    {
        return $this->idcommande;
    }

    /**
     * @param \Commande $idcommande
     */
    public function setIdcommande($idcommande)
    {
        $this->idcommande = $idcommande;
    }

}

