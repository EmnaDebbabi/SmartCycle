<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cadeau
 *
 * @ORM\Table(name="cadeau")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\CadeauRepository")
 */
class Cadeau
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codecadeau", type="string", length=255)
     */
    private $codecadeau;


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
     * Set codecadeau
     *
     * @param string $codecadeau
     *
     * @return Cadeau
     */
    public function setCodecadeau($codecadeau)
    {
        $this->codecadeau = $codecadeau;

        return $this;
    }

    /**
     * Get codecadeau
     *
     * @return string
     */
    public function getCodecadeau()
    {
        return $this->codecadeau;
    }
}

