<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {

        /** @var UserPasswordEncoderInterface $encoder */
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->load_users($manager);
        $this->load_messages($manager);
    }

    public function load_users(ObjectManager $manager)
    {

            $user = new User();
            $user->setUserName('Jack.Sparrow');
            $user->setEmail('allo_jack@lolmail.com');
            $user->setLanguage("en");
            $user->setStatus(0);
            $user->setPassword($this->encoder->encodePassword(
                $user,
                'allo1212'
            ));


            $manager->persist($user);

            $this->setReference('Jack Sparrow',$user);

        $manager->flush();
    }

    public function load_messages(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $message = new Message();
            $message->setText('Captain Jack Sparrow!!!' . rand(15, 100));
            $message->setStatus(1);
            $message->setUser($this->getReference('Jack Sparrow'));
            $manager->persist($message);

        }
        $manager->flush();
    }
}
