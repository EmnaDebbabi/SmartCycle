<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categorietopics
 *
 * @ORM\Table(name="categorietopics", indexes={@ORM\Index(name="cat_id", columns={"cat_id"})})
 * @ORM\Entity
 */
class Categorietopics
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cat_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $catId;

    /**
     * @var string
     *
     * @ORM\Column(name="cat_name", type="string", length=255, nullable=false)
     */
    private $catName;

    /**
     * @var string
     *
     * @ORM\Column(name="cat_description", type="string", length=255, nullable=false)
     */
    private $catDescription;

    /**
     * @return int
     */
    public function getCatId()
    {
        return $this->catId;
    }

    /**
     * @param int $catId
     */
    public function setCatId($catId)
    {
        $this->catId = $catId;
    }

    /**
     * @return string
     */
    public function getCatName()
    {
        return $this->catName;
    }

    /**
     * @param string $catName
     */
    public function setCatName($catName)
    {
        $this->catName = $catName;
    }

    /**
     * @return string
     */
    public function getCatDescription()
    {
        return $this->catDescription;
    }

    /**
     * @param string $catDescription
     */
    public function setCatDescription($catDescription)
    {
        $this->catDescription = $catDescription;
    }


}

