<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\Listener\Entity\UserListener"})
 * @ORM\Table(name="app_user")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    private $roles;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $active;


    const ROLE_DEFAULT     = 'ROLE_USER';
    const ROLE_ADMIN       = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';


    public function __construct()
    {
        $this->active = true;
        $this->roles  = [];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param null|string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($this->roles);
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role)
    {
        return in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function removeRole(string $role): User
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = array_map(
            function ($role) {
                return mb_strtoupper($role);
            },
            array_unique($roles)
        );

        return $this;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole(string $role): User
    {
        $role = mb_strtoupper($role);

        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see User::getActive()
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setActive(bool $active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->active,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->active
            ) = unserialize($serialized);
    }
}