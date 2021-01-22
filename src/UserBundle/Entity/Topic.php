<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * Topic
 *
 * @ORM\Table(name="topic", indexes={@ORM\Index(name="topic_id", columns={"topic_id"}), @ORM\Index(name="topic_cat", columns={"topic_cat"}), @ORM\Index(name="topic_by", columns={"topic_by"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="UserBundle\Repository\TopicRepository")
 */
class Topic
{
    /**
     * @var integer
     *
     * @ORM\Column(name="topic_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $topicId;

    /**
     * @var string
     *
     * @ORM\Column(name="topic_title", type="string", length=255, nullable=false)
     */
    private $topicTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="topic_subject", type="string", length=255, nullable=false)
     */
    private $topicSubject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="topic_date", type="date", nullable=true)
     */
    private $topicDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="etat", type="integer", nullable=true)
     */
    private $etat;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer", nullable=true)
     */
    private $rate;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer", nullable=true)
     */
    private $likes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dispo", type="boolean", nullable=true)
     */
    private $dispo;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=100, nullable=false)
     */
    private $photo;

    /**
     * @var \Membre
     *
     * @ORM\ManyToOne(targetEntity="Membre")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="topic_by", referencedColumnName="id")
     * })
     */
    private $topicBy;
    public function __construct()
    {
        $this->topicDate = new \DateTime();
        $this->reply = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * @var \Categorietopics
     *
     * @ORM\ManyToOne(targetEntity="Categorietopics")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="topic_cat", referencedColumnName="cat_id")
     * })
     */
    private $topicCat;

    /**
     * @return int
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * @param int $topicId
     */
    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;
    }

    /**
     * @return string
     */
    public function getTopicTitle()
    {
        return $this->topicTitle;
    }

    /**
     * @param string $topicTitle
     */
    public function setTopicTitle($topicTitle)
    {
        $this->topicTitle = $topicTitle;
    }

    /**
     * @return string
     */
    public function getTopicSubject()
    {
        return $this->topicSubject;
    }

    /**
     * @param string $topicSubject
     */
    public function setTopicSubject($topicSubject)
    {
        $this->topicSubject = $topicSubject;
    }

    /**
     * @return \DateTime
     */
    public function getTopicDate()
    {
        return $this->topicDate;
    }

    /**
     * @param \DateTime $topicDate
     */
    public function setTopicDate($topicDate)
    {
        $this->topicDate = $topicDate;
    }

    /**
     * @return int
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param int $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param int $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
    }

    /**
     * @return bool
     */
    public function isDispo()
    {
        return $this->dispo;
    }

    /**
     * @param bool $dispo
     */
    public function setDispo($dispo)
    {
        $this->dispo = $dispo;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return \Membre
     */
    public function getTopicBy()
    {
        return $this->topicBy;
    }

    /**
     * @param \Membre $topicBy
     */
    public function setTopicBy($topicBy)
    {
        $this->topicBy = $topicBy;
    }

    /**
     * @return \Categorietopics
     */
    public function getTopicCat()
    {
        return $this->topicCat;
    }

    /**
     * @param \Categorietopics $topicCat
     */
    public function setTopicCat($topicCat)
    {
        $this->topicCat = $topicCat;
    }
    /**
     * @Assert\File(maxSize="500k")
     */
    public $file ;

    /**
     * @return mixed
     */
    public function getfile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setfile($file)
    {
        $this->file = $file;
    }
    public function getWebPath(){

        return null===$this->photo ? null : $this->getUploadDir().'/'.$this->photo;
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
        if(!$this->topicId)
        {
            $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        }else
        {

            $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
        }
        $this->setPhoto($this->file->getClientOriginalName());


    }

    public function __call($name, $arguments)
    {
        $this->reply = new ArrayCollection();

    }

    /**
     * Add reply
     *
     * @param \UserBundle\Entity\Reply $replyy
     *
     * @return Topic
     */
    public function addCommentaire(\UserBundle\Entity\Reply $replyy)
    {
        $this->reply[] = $replyy;

        return $this;
    }



}

