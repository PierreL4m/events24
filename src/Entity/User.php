<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Controller\Api\GetUserRole;
use App\Controller\Api\GetMe;
use App\Controller\Api\PatchWorkingCandidat;
use App\Controller\Api\GetUserByEmail;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique",columns={"email"})}
 * )
 * @UniqueEntity(fields={"email"}, message="Vous avez déjà créé un compte")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"l4m" = "L4MUser", "exposant" = "ExposantUser","rh" = "RhUser", "candidate" = "CandidateUser", "scan" = "ScanUser","exposant_scan" = "ExposantScanUser","onsite" = "OnsiteUser"})
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *           },
 *          "get_user_role"={
 *              "method" ="GET",
 *              "path" = "/user",
 *              "controller"=GetUserRole::class,
 *              "read"=false
 *          },
 *          "get_me"={
 *              "method" ="GET",
 *              "path" = "/user/me",
 *              "controller"=GetMe::class,
 *              "read"=false,
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *          },
 *           "get_user_mail"={
 *               "method" ="GET",
 *               "path" = "/userByMail/{data}",
 *               "controller"=GetUserByEmail::class,
 *               "read"=false,
 *               "normalization_context"={"groups"={"read:collection_user"}}
 *           }
 *     }
 * )
 **/
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    public function getResponsableBises()
    {
        return null;
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Groups({"read:collection_user"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_token"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     */
    protected $id;

    /**

     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message = "Merci de saisir votre prénom"
     * )
     * @Assert\Type("string")
     * @Groups({"read:collection_user"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_register"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message = "Merci de saisir votre nom"
     * )
     * @Assert\Type("string")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_register"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:collection_user"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:patch_user_device"})
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(
     *     message = "Merci de saisir votre email"
     * )
     * @Assert\Email
     * @Groups({"read:collection_user"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_register"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     */
    protected $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime|null
     * @Groups({"read:patch_user_device"})
     */
    protected $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length = 20)
     * @Assert\NotBlank(
     *     message = "Merci de saisir votre numéro de téléphone"
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 10,
     *      max = 20,
     *      minMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres",
     *      maxMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres"
     * )
     * @Groups({"read:collection_user"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_register"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mobile;

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $this->formatPhone($mobile);
    }

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=50, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Type("string")
     * @Groups({"read:patch_user_device"})
     */
    private $device;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="responsable")
     * @Groups({"read:collection_user"})
     */
    private $participations;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Timestamp", mappedBy="createdBy", cascade={"all"}, orphanRemoval=true)
     */
    private $creates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Timestamp", mappedBy="updatedBy", cascade={"all"}, orphanRemoval=true)
     */
    private $updates;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Type("string")
     * @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}/",
     *  message="Le mot de passe doit avoir au moins 8 caractères, avec au moins un chiffre, une majuscule et une minuscule."
     * )
     * @Groups({"read:collection_register"})
     * @Groups({"read:patch_profile_candidate"})
     */
    protected $plainPassword;

    //    /**
    //     * @ORM\OneToMany(targetEntity="App\Entity\Oauth\RefreshToken", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
    //     */
    //    private $refreshTokens;
    //
    //    /**
    //     * @ORM\OneToMany(targetEntity="App\Entity\Oauth\AuthCode", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
    //     */
    //    private $authCodes;
    //
    //    /**
    //     * @ORM\OneToMany(targetEntity="App\Entity\Oauth\AccessToken", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
    //     */
    //    private $accessTokens;

    /**
     * @ORM\ManyToOne(targetEntity="Origin", inversedBy="candidateParticipations")
     * @ORM\JoinColumn(nullable=true,onDelete="SET NULL")
     */
    private $origin;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->updates = new ArrayCollection();
        $this->creates = new ArrayCollection();
        $this->refreshTokens = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
        $this->authCodes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname.' '.$this->lastname;
    }

    abstract function getType();

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = ucwords($firstname);
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = mb_strtoupper($lastname);
    }

    /**
     * Add participation
     *
     * @param \App\Entity\Participation $participation
     *
     * @return User
     */
    public function addParticipation(\App\Entity\Participation $participation)
    {
        $this->participations[] = $participation;
        $participation->setResponsable($this);

        return $this;
    }

    /**
     * Remove participation
     *
     * @param \App\Entity\Participation $participation
     */
    public function removeParticipation(\App\Entity\Participation $participation)
    {
        $this->participations->removeElement($participation);
    }

    /**
     * Get participations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param integer $phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $this->formatPhone($phone);

        return $this;
    }

    private function formatPhone($phone)
    {

        $phone = preg_replace('#\D#U', '', $phone);
        if(strlen($phone) != 10) return $phone;

        for($pos = 8; $pos > 0; $pos-= 2){
            $phone = substr($phone, 0, $pos) .'.'. substr($phone, $pos);
        }
        return $phone;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     *
     * @return self
     */
    public function setDevice($device)
    {
        $this->device = $device;

        return $this;
    }

    ///////////////////////////////////
    //////////BASE USER///////////////
    /// /////////////////////////////

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $username;

    /**
     * Sets the username.
     *
     * @param string $username
     *
     * @return static
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }



    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var bool
     */
    protected $enabled;

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    /**
     * The salt to use for hashing.
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $salt;

    /**
     * @param string|null $salt
     *
     * @return static
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Sets the last login time.
     *
     * @param \DateTime|null $time
     *
     * @return static
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Random string sent to the user email address in order to verify it.
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    protected $confirmationToken;

    /**
     * Gets the confirmation token.
     *
     * @return string|null
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Sets the confirmation token.
     *
     * @param string|null $confirmationToken
     *
     * @return static
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @var \DateTime|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param null|\DateTime $date
     *
     * @return static
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    //////////////////////////////////
    /////////USER INTERFACE//////////
    ////////////////////////////////

    /**
     * @ORM\Column(type="array")
     * @Groups({"read:collection_role"})
     * @Groups({"read:collection_user"})
     */
    private $roles = [];

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }
    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     *  @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}/",
     *  message="Le mot de passe doit avoir au moins 8 caractères, avec au moins un chiffre, une majuscule et une minuscule."
     * )
     */
    private $password;

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier() {
        return ''.$this->getId();
    }

    /**
     * @internal
     */

    public function __serialize(): array
    {
        return get_object_vars($this);
    }

    final public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        foreach($data as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @internal
     */
    final public function unserialize($serialized)
    {
        $this->__unserialize(unserialize($serialized));
    }
    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param mixed $origin
     *
     * @return self
     */
    public function setOrigin(\App\Entity\Origin $origin)
    {
        $this->origin = $origin;

        return $this;
    }
}
