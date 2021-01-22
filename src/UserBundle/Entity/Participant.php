<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participant
 *
 * @ORM\Table(name="participant", indexes={@ORM\Index(name="participant_ibfk_1", columns={"idM"}), @ORM\Index(name="participant_ibfk_2", columns={"idevent"})})
 * @ORM\Entity(repositoryClass="UserBundle\Repository\participationRepository")
 */
class Participant
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idP", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idp;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255, nullable=false)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="avis", type="string", length=255, nullable=false)
     */
    private $avis;

    /**
     * @var \Membre
     *
     * @ORM\ManyToOne(targetEntity="Membre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idM", referencedColumnName="id")
     * })
     */
    private $idm;

    /**
     * @var \Evttesting
     *
     * @ORM\ManyToOne(targetEntity="Evttesting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idevent", referencedColumnName="id")
     * })
     */
    private $idevent;

    /**
     * @return int
     */
    public function getIdp()
    {
        return $this->idp;
    }

    /**
     * @param int $idp
     */
    public function setIdp($idp)
    {
        $this->idp = $idp;
    }

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
     * @return string
     */
    public function getAvis()
    {
        return $this->avis;
    }

    /**
     * @param string $avis
     */
    public function setAvis($avis)
    {
        $this->avis = $avis;
    }

    /**
     * @return \Membre
     */
    public function getIdm()
    {
        return $this->idm;
    }

    /**
     * @param \Membre $idm
     */
    public function setIdm($idm)
    {
        $this->idm = $idm;
    }

    /**
     * @return \Evttesting
     */
    public function getIdevent()
    {
        return $this->idevent;
    }

    /**
     * @param \Evttesting $idevent
     */
    public function setIdevent($idevent)
    {
        $this->idevent = $idevent;
    }


}

