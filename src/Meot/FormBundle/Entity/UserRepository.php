<?php
namespace Meot\FormBundle\Entity;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserProviderInterface;
use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements ShibbolethUserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin FormBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function createUser(ShibbolethUserToken $token) {
        // Create user object using shibboleth attributes stored in the token.
        $user = new User();
        $user->setUsername($token->getUsername());
        $user->setPassword('randompassword');
        #$user->setSurname($token->getSurname());
        #$user->setGivenName($token->getGivenName());
        #$user->setEmail($token->getMail());
        // If you like, you can also add default roles to the user based on shibboleth attributes. E.g.:
        //if ($token->isStudent()) $user->addRole('ROLE_STUDENT');
        //elseif ($token->isStaff()) $user->addRole('ROLE_STAFF');
        //else $user->addRole('ROLE_GUEST');

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
/*        $class = get_class($user);
        var_dump($class);exit;
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());*/
    }

    public function supportsClass($class)
    {
        return $class === 'Meot\FormBundle\Entity\User';
//        return $this->getEntityName() === $class
//            || is_subclass_of($class, $this->getEntityName());
    }
}
