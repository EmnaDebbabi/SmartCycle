<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Repository\AssociationRepository;


/**
 * Association
 *
 * @ORM\Table(name="association")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\AssociationRepository")
 */
class Association
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_association", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAssociation;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_association", type="string", length=50, nullable=false)
     */
    private $nomAssociation;


    /**
     * @var \DateTime $date_creation
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=45, nullable=false)
     */
    private $adresse;

    /**
     * @var integer
     *
     * @ORM\Column(name="capital", type="integer", nullable=false)
     */
    private $capital;


    /**
     * @var \Membre
     *
     * @ORM\ManyToOne(targetEntity="Membre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chef_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $chefId;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", length=16777215, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    public $nomImage;
    /**
     * @Assert\File(maxSize="500K")
     */
    public $file;

    public function getWebPath()
    {
        return null === $this->nomImage ? null : $this->getUploadDir() . '/' . $this->nomImage; //get uplodadDir blech parenthéses lihna ,ama l json ma9rahech
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'images';
    }

    public function uploadProfilePicture()
    {
        if (null === $this->file) {
            return ;
        }
        else{

            $this->file->move($this->getUploadRootDir(),$this->file->getClientOriginalName());
            $this->nomImage=$this->file->getClientOriginalName();
            $this->file=null;
        }
    }

    /**
     * Set nomImage
     *
     * @param string $nomImage
     *
     * @return Categorie
     */
    public function setNomImage($nomImage)
    {
        $this->nomImage == $nomImage;
        return $this;
    }

    /**
     * Get nomImage
     *
     * @return string
     */
    public function getNomImage()
    {
        return $this->nomImage;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Membre", inversedBy="idAssociation")
     * @ORM\JoinTable(name="adherent",
     *   joinColumns={
     *     @ORM\JoinColumn(name="id_association", referencedColumnName="id_association")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="id_membre", referencedColumnName="id")
     *   }
     * )
     */
    private $idMembre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idMembre = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * @return int
     */
    public function getIdAssociation()
    {
        return $this->idAssociation;
    }

    /**
     * @param int $idAssociation
     */
    public function setIdAssociation($idAssociation)
    {
        $this->idAssociation = $idAssociation;
    }

    /**
     * @return string
     */
    public function getNomAssociation()
    {
        return $this->nomAssociation;
    }

    /**
     * @param string $nomAssociation
     */
    public function setNomAssociation($nomAssociation)
    {
        $this->nomAssociation = $nomAssociation;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param \DateTime $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @return int
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * @param int $capital
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;
    }

    /**
     * @return int
     */
    public function getChefId()
    {
        return $this->chefId;
    }

    /**
     * @param int $chefId
     */
    public function setChefId($chefId)
    {
        $this->chefId = $chefId;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @param string $adresse
     */
    public function setIMG($nimg)
    {
        $this->nomImage = $nimg;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdMembre()
    {
        return $this->idMembre;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $idMembre
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;
    }
}
