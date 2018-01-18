<?php
/**
 * Created by PhpStorm.
 * User: timotheecorrado
 * Date: 10/01/2018
 * Time: 18:28
 */

namespace App\Listener\Entity;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    }

    /** @PrePersist
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function prePersistHandler(User $user, LifecycleEventArgs $event)
    {
        if ($user->getPlainPassword() !== null)
        {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encodedPassword);
        }
        else
        {

        }
    }
}