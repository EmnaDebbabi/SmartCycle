<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="id_cmd", columns={"id_cmd"}), @ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 */
class Commande
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cmd", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCmd;

    /**
     * @var \DateTime  $date
     *@Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date ;

    /**
     * @var \DateTime  $dateexp
     *@Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dateexp", type="datetime", nullable=true)
     */
    private $dateexp ;

    /**
     * @var \Membre
     *
     * @ORM\ManyToOne(targetEntity="Membre",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=30, nullable=true)
     */
    private $etat;
    /**
     * @var string
     *
     * @ORM\Column(name="payement", type="string", length=30, nullable=true)
     */
    private $payement;
    /**
     * @var string
     *
     * @ORM\Column(name="point", type="string", length=50 , nullable=true)
     */
    private $point;
    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=true)
     */
    private $total;

    /**
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param string $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return \Membre
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param \Membre $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getIdCmd()
    {
        return $this->idCmd;
    }

    /**
     * @param int $idCmd
     */
    public function setIdCmd($idCmd)
    {
        $this->idCmd = $idCmd;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return \DateTime
     */
    public function getDateexp()
    {
        return $this->dateexp;
    }

    /**
     * @param \DateTime $dateexp
     */
    public function setDateexp($dateexp)
    {
        $this->dateexp = $dateexp;
    }

    /**
     * @return string
     */
    public function getPayement()
    {
        return $this->payement;
    }

    /**
     * @param string $payement
     */
    public function setPayement($payement)
    {
        $this->payement = $payement;
    }

    /**
     * @return string
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param string $point
     */
    public function setPoint($point)
    {
        $this->point = $point;
    }

}

