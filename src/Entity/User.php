<?php

namespace App\Entity;

use App\Entity\Message;
use App\Service\Helper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"user_name"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, \Serializable
{
    public function __construct()
    {
        $date = new \DateTime();
        $this->created_at = $date;
        $this->messages = new ArrayCollection();
        $this->setAuthKey(Helper::generateRandomString(30));
    }

    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=50)
     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     *@Assert\NotBlank()
     *@Assert\Length(min=8, max="4096")
     *
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $language = "en";

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $status = 1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="user")
     */
    private $messages;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser_info;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $auth_key;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }


    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }


    public function getStatus(): ?int
    {
        return $this->status;
    }


    public function setStatus($status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * @param Message $message
     * @return User
     */
    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {


       if($this->status == 2)
        {
            return [
                'ROLE_MODERATOR'
            ];
        }
        elseif($this->status == 3)
        {
            return [
                'ROLE_ADMIN'
            ];
        }
        return ['ROLE_USER'];
    }
    public function getRolesString()
    {

        if($this->status == 1)
            return 'USER';
        elseif($this->status == 2)
        {
            return 'MODERATOR';
        }
        elseif($this->status == 3)
        {
            return 'ADMIN';
        }
    }
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
       return serialize([
           $this->id,
           $this->user_name,
           $this->email,
           $this->password
       ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list($this->id,
            $this->user_name,
            $this->email,
            $this->password) = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getBrowserInfo(): ?string
    {
        return $this->browser_info;
    }

    public function setBrowserInfo(?string $browser_info): self
    {
        $this->browser_info = $browser_info;

        return $this;
    }
    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->user_name;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function setAuthKey(string $auth_key): self
    {
        $this->auth_key = $auth_key;

        return $this;
    }
}
