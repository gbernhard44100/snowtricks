<?php

namespace GB\TricksBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trick
 *
 * @ORM\Table(name="trick")
 * @ORM\Entity(repositoryClass="GB\TricksBundle\Repository\TrickRepository")
 * @UniqueEntity(fields={"name"}, message="Le nom de la figure est déjà été utilisé.")
 */
class Trick
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var \stdClass
     *
     * @ORM\Column(name="category", type="string")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var int
     *
     * @ORM\Column(name="frontImage", type="integer", nullable=true)
     */
    private $frontImage = 1;

    /**
     *
     * @ORM\OneToMany(targetEntity="GB\TricksBundle\Entity\Message", cascade={"persist", "remove"},
     * mappedBy="trick")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="GB\TricksBundle\Entity\Picture", cascade={"persist", "remove"},
     * mappedBy="trick", orphanRemoval=true)
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity="GB\TricksBundle\Entity\Video", cascade={"persist", "remove"},
     * mappedBy="trick")
     */
    private $videos;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Trick
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Trick
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
     * Set category
     *
     * @param \stdClass $category
     *
     * @return Trick
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \stdClass
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set frontImage
     *
     * @param integer $frontImage
     *
     * @return Trick
     */
    public function setFrontImage($frontImage)
    {
        $this->frontImage = $frontImage;

        return $this;
    }

    /**
     * Get frontImage
     *
     * @return int
     */
    public function getFrontImage()
    {
        return $this->frontImage;
    }

    public function addPicture(Picture $picture)
    {
        $this->pictures[] = $picture;
        $picture->setTrick($this);

        return $this;
    }

    public function removePicture(Picture $picture)
    {
        $this->pictures->removeElement($picture);
        $picture->setTrick(null);
    }

    public function addVideo(Video $video)
    {
        $this->videos[] = $video;
        $video->setTrick($this);

        return $this;
    }

    public function removeVideo(Video $video)
    {
        $this->videos->removeElement($video);
        $video->setTrick(null);
    }

    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
        $message->setTrick($this);

        return $this;
    }

    /**
     * Remove message
     *
     * @param \GB\TricksBundle\Entity\Message $message
     */
    public function removeMessage(\GB\TricksBundle\Entity\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

}
