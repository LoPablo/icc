<?php

namespace App\Security\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\UserCreatorInterface;
use LightSaml\SpBundle\Security\User\UsernameMapperInterface;
use Ramsey\Uuid\Uuid;
use SchoolIT\CommonBundle\Saml\ClaimTypes;

class UserCreator implements UserCreatorInterface {

    private $em;
    private $userMapper;
    private $usernameMapper;

    public function __construct(EntityManagerInterface $em, UserMapper $userMapper, UsernameMapperInterface $usernameMapper) {
        $this->em = $em;
        $this->userMapper = $userMapper;
        $this->usernameMapper = $usernameMapper;
    }

    /**
     * @inheritDoc
     */
    public function createUser(Response $response) {
        // Second chance: map user by ID
        $id = $response->getFirstAssertion()
            ->getFirstAttributeStatement()
            ->getFirstAttributeByName(ClaimTypes::ID)
            ->getFirstAttributeValue();

        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['idpId' => $id ]);

        if($user === null) {
            $user = (new User())
                ->setIdpId(Uuid::fromString($id));
        }

        $this->userMapper->mapUser($user, $response);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}