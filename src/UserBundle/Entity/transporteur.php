<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * transporteur
 * @ORM\Table(name="transporteur")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\transporteurRepository")
 */
class transporteur
{
    /**
     * @var int
     * @ORM\Column(name="id_transporteur", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var int
     * @ORM\Column(name="cin", type="integer")
     */
    private $cin;

    /**
     * @var string
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var int
     * @ORM\Column(name="telephone", type="integer")
     */
    private $telephone;

    /**
     * @var string
     * @ORM\Column(name="permit", type="string", length=255)
     */
    private $permit;


    /**
     * Get id
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
     * Set nom
     * @param string $nom
     * @return transporteur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     * @param string $prenom
     * @return transporteur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set cin
     * @param integer $cin
     * @return transporteur
     */
    public function setCin($cin)
    {
        $this->cin = $cin;

        return $this;
    }

    /**
     * Get cin
     * @return int
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * Set adresse
     * @param string $adresse
     * @return transporteur
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set telephone
     * @param integer $telephone
     * @return transporteur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     * @return int
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set permit
     * @param string $permit
     * @return transporteur
     */
    public function setPermit($permit)
    {
        $this->permit = $permit;

        return $this;
    }

    /**
     * Get permit
     * @return string
     */
    public function getPermit()
    {
        return $this->permit;
    }
}

