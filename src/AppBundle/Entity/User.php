<?php

namespace GB\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use GB\UserBundle\Validator\UserExist;
use GB\UserBundle\Validator\Registration;
use GB\UserBundle\Validator\Password;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="GB\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"userName"}, message="Le nom d'utilisateur a déjà été utilisé.", groups={"registration"})
 * @UniqueEntity(fields={"email"}, message="Cette adresse email a déjà été utilisée.", groups={"registration"})
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, \Serializable
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
     * @UserExist(groups={"userExist"})
     * @Registration(groups={"login"})
     * @ORM\Column(name="userName", type="string", length=255, unique=true)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="profilPictureUrl", type="string", length=255, nullable=true, unique=true)
     */
    private $profilPictureUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     * @Password(groups={"login"})
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="validationToken", type="string", length=255, nullable=true)
     */
    private $validationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="passwordToken", type="string", length=255, nullable=true)
     */
    private $passwordToken;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = array('IS_AUTHENTICATED_REMEMBERED');
    private $file;
    private $tempFileName;

    public function eraseCredentials()
    {
        
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->userName,
            $this->password,
            $this->email,
            $this->profilPictureUrl,
            $this->validationToken,
            $this->passwordToken,
            $this->roles
        ));
    }

    public function unserialize($serialized)
    {
        list (
                $this->id,
                $this->userName,
                $this->password,
                $this->email,
                $this->profilPictureUrl,
                $this->validationToken,
                $this->passwordToken,
                $this->roles
                ) = unserialize($serialized);
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
     * Set userName
     *
     * @param string $userName
     *
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set profilPictureUrl
     *
     * @param string $profilPictureUrl
     *
     * @return User
     */
    public function setProfilPictureUrl($profilPictureUrl)
    {
        $this->profilPictureUrl = $profilPictureUrl;
        return $this;
    }

    /**
     * Get profilPictureUrl
     *
     * @return string
     */
    public function getProfilPictureUrl()
    {
        return $this->profilPictureUrl;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set validationToken
     *
     * @param string $validationToken
     *
     * @return User
     */
    public function setValidationToken($validationToken)
    {
        $this->validationToken = $validationToken;

        return $this;
    }

    /**
     * Get validationToken
     *
     * @return string
     */
    public function getValidationToken()
    {
        return $this->validationToken;
    }

    /**
     * Set passwordToken
     *
     * @param string $passwordToken
     *
     * @return User
     */
    public function setPasswordToken($passwordToken)
    {
        $this->passwordToken = $passwordToken;

        return $this;
    }

    /**
     * Get passwordToken
     *
     * @return string
     */
    public function getPasswordToken()
    {
        return $this->passwordToken;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        if ($this->profilPictureUrl !== null) {
            $this->tempFileName = $this->profilPictureUrl;
            $this->profilPictureUrl = null;
        }
    }

    /**
     * FUNCTIONS TO MANAGE THE PROFIL PICTURE
     */

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
        $this->profilPictureUrl = md5(uniqid()) . '.' . $this->file->getClientOriginalExtension();
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
         * Delete the old folder if exists.
         */
        if ($this->tempFileName !== null) {
            $oldFile = $this->getUploadRootDir() . $this->getUploadDir() . '/' . $this->tempFileName;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->file->move($this->getUploadRootDir() . $this->getUploadDir(), $this->profilPictureUrl);

        /**
         * Resizing the picture after moving it into the Upload folder:
         */
        $source = imagecreatefromjpeg($this->getUploadDir() . '/' . $this->profilPictureUrl);
        $finalSizePicture = imagecreatetruecolor(150, 150);
        imagecopyresampled($finalSizePicture, $source, 0, 0, 0, 0, imagesx($finalSizePicture), imagesy($finalSizePicture), imagesx($source), imagesy($source));
        imagejpeg($finalSizePicture, $this->getUploadRootDir() . $this->getUploadDir() . '/' . $this->profilPictureUrl);
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFileName = $this->getUploadRootDir() . $this->getUploadDir() . '/' . $this->profilPictureUrl;
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
        return 'pictures/profils';
    }

    public function getUploadRootDir()
    {
        return '';
    }

    public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->profilPictureUrl;
    }

}
