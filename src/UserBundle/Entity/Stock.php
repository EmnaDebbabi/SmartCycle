<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\StockRepository")
 */
class Stock
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cat", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCat;

    /**
     * @var float
     *
     * @ORM\Column(name="quantitedispo", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantitedispo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="prixuni", type="float", precision=10, scale=0, nullable=false)
     */
    private $prixuni;

    /**
     * @var string
     *
     * @ORM\Column(name="nomimage", type="string", length=255, nullable=true)
     */
    public $nomimage;
    /**
     * @Assert\File(maxSize="500k")
     */
    public $file ;
    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * @return int
     */
    public function getIdCat()
    {
        return $this->idCat;
    }

    /**
     * @param int $idCat
     */
    public function setIdCat($idCat)
    {
        $this->idCat = $idCat;
    }

    /**
     * @return float
     */
    public function getQuantitedispo()
    {
        return $this->quantitedispo;
    }

    /**
     * @param float $quantitedispo
     */
    public function setQuantitedispo($quantitedispo)
    {
        $this->quantitedispo = $quantitedispo;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getPrixuni()
    {
        return $this->prixuni;
    }

    /**
     * @param float $prixuni
     */
    public function setPrixuni($prixuni)
    {
        $this->prixuni = $prixuni;
    }

    /**
     * Get nomimage
     * @return  string
     */
    public function getNomimage()
    {
        return $this->nomimage;
    }

    /**
     * Set nomimage
     * @param string $nomimage
     * @return Categorie
     */
    public function setNomimage($nomimage)
    {
        $this->nomimage = $nomimage;
        return null ;
    }


    public function getWebPath(){

        return null===$this->nomimage ? null : $this->getUploadDir().'/'.$this->nomimage;
    }
    protected  function  getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();


    }
    protected function getUploadDir()
    {
        return 'ImagesStock' ;

    }
    public function uploadProfilePicture()
    {

        if (null === $this->file)
        {
            return;
        }
        if(!$this->idCat)
        {
            $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        }else
        {

            $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        }
        $this->setNomimage($this->file->getClientOriginalName());


    }




}

