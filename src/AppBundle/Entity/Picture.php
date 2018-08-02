<?php

namespace GB\TricksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Picture
 *
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="GB\TricksBundle\Repository\PictureRepository")
 * @UniqueEntity(fields={"url"}, message={"Le nom de la photo a déjà été utilisé."})
 * @ORM\HasLifecycleCallbacks
 */
class Picture
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
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     * @Assert\Regex(pattern="(\.jpeg|\.jpg|\.png)$",
     * message="Le fichier à charger doit être sous format jpeg, jpg ou png")
     */
    private $url;

    /**
     *
     * @ORM\ManyToOne(targetEntity="GB\TricksBundle\Entity\Trick",
     *  inversedBy="pictures")
     * @ORM\JoinColumn(name="trick_id", referencedColumnName="id")
     */
    private $trick;
    private $file;
    private $tempFileName;

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
     * Set url
     *
     * @param string $url
     *
     * @return Picture
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set trick
     *
     * @param \GB\TricksBundle\Entity\Trick $trick
     *
     * @return Picture
     */
    public function setTrick(\GB\TricksBundle\Entity\Trick $trick = null)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick
     *
     * @return \GB\TricksBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        if ($this->url !== null) {
            $this->tempFileName = $this->url;
            $this->url = null;
        }
    }

    /**
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if ($this->file === null) {
            return;
        }
        $this->url = md5(uniqid()) . '.' . $this->file->getClientOriginalExtension();
    }

    /**
     * 
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if ($this->file === null) {
            return;
        }

        /**
         * Suppression de l'ancien fichier si il existe.
         */
        if ($this->tempFileName !== null) {
            $oldFile = $this->getUploadRootDir() . $this->getUploadDir() . $this->tempFileName;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        $this->file->move($this->getUploadRootDir() . $this->getUploadDir(), $this->url);
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFileName = $this->getUploadRootDir() . $this->getUploadDir() . '/' . $this->url;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFileName)) {
            unlink($this->tempFileName);
        }
    }

    public function getUploadDir()
    {
        return 'pictures/tricks';
    }

    public function getUploadRootDir()
    {
        return '';
    }

    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->url;
    }

}
