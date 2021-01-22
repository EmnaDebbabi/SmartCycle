<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\produitRepository")
 */
class produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_produit", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nomproduit", type="string", length=255)
     */
    private $nomproduit;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="categorie",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="typeproduit", referencedColumnName="id")
     * })
     */
    private $typeproduit;

    /**
     * @var string
     *
     * @ORM\Column(name="quantiteproduit", type="decimal", precision=10, scale=0)
     */
    private $quantiteproduit;

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
        return null === $this->nomImage ? null : $this->getUploadDir . '/' . $this->nomImage;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'imageProduits';
    }

    public function uploadProfilePicture()
    {
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        $this->nomImage = $this->file->getClientOriginalName();
        $this->file = null;
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */

    private $status;

    /**
     * @var \DateTime  $dat
     *@Gedmo\Timestampable(on="create")
     * @ORM\Column(name="dat", type="datetime", nullable=true)
     */
    private $dat;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomproduit
     *
     * @param string $nomproduit
     *
     * @return produit
     */
    public function setNomproduit($nomproduit)
    {
        $this->nomproduit = $nomproduit;

        return $this;
    }

    /**
     * Get nomproduit
     *
     * @return string
     */
    public function getNomproduit()
    {
        return $this->nomproduit;
    }

    /**
     * Set typeproduit
     *
     * @param string $typeproduit
     *
     * @return produit
     */
    public function setTypeproduit($typeproduit)
    {
        $this->typeproduit = $typeproduit;

        return $this;
    }

    /**
     * Get typeproduit
     *
     * @return string
     */
    public function getTypeproduit()
    {
        return $this->typeproduit;
    }

    /**
     * Set quantiteproduit
     *
     * @param string $quantiteproduit
     *
     * @return produit
     */
    public function setQuantiteproduit($quantiteproduit)
    {
        $this->quantiteproduit = $quantiteproduit;

        return $this;
    }

    /**
     * Get quantiteproduit
     *
     * @return string
     */
    public function getQuantiteproduit()
    {
        return $this->quantiteproduit;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return produit
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return produit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dat
     *
     * @param \DateTime $dat
     *
     * @return produit
     */
    public function setDat($dat)
    {
        $this->dat = $dat;

        return $this;
    }

    /**
     * Get dat
     *
     * @return \DateTime
     */
    public function getDat()
    {
        return $this->dat;
    }
}