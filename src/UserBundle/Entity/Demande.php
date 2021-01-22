<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Repository\AssociationRepository;


/**
 * Demande
 *
 * @ORM\Table(name="demande", indexes={@ORM\Index(name="idM", columns={"idM", "idA"}), @ORM\Index(name="idA", columns={"idA"}), @ORM\Index(name="IDX_2694D7A5B1BBBA33", columns={"idM"})})
 * @ORM\Entity(repositoryClass="UserBundle\Repository\DemandeRepository")
 */
class Demande
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
     * @var string
     * @ORM\Column(name="etat",type="string",length=45,nullable=false)
     */
    private $etat;


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
     * @var \Association
     *
     * @ORM\ManyToOne(targetEntity="Association")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idA", referencedColumnName="id_association")
     * })
     */
    private $ida;

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
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return \Association
     */
    public function getIda()
    {
        return $this->ida;
    }

    /**
     * @param \Association $ida
     */
    public function setIda($ida)
    {
        $this->ida = $ida;
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

//    public function notificationsOnCreate(NotificationBuilder $builder)
//    {
////         $notif = new Notification();
////        $notif->setIdm($this->getIdm());
////        $notif
////            ->setTitle('demande d\'adhération !')
////            ->setDescription($this->getEtat())
////            ->setRoute('user_homepage')
////            ->setParameters(array('id'=> $this->getId()));
////        $builder->addNotification($notif);
////        return $builder;
//    }
//
//    public function notificationsOnUpdate(NotificationBuilder $builder)
//    {
//        $notif = new Notification();
//        $notif->setIdm($this->getIdm());
//        if($this->getEtat()=="valider"){
//            $msg ="demande accepter !";
//        } else $msg ="demande refuser";
//        $notif
//            ->setTitle($msg)
//            ->setDescription($this->getEtat())
//            ->setRoute('user_homepage')
//            ->setParameters(array('id'=> $this->getId()));
//        $builder->addNotification($notif);
//        return $builder;
//    }
//
//    public function notificationsOnDelete(NotificationBuilder $builder)
//    {
//        // TODO: Implement notificationsOnDelete() method.
//    }


}
