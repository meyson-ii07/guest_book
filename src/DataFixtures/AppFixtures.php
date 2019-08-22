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
            $user->setUserName('Jack.Sparrow.Admin');
            $user->setEmail('meyson.rlt@gmail.com');
            $user->setLanguage("en");
            $user->setStatus(3);
            $user->setPassword($this->encoder->encodePassword(
                $user,
                'adminpassword654478'
            ));

             $this->setReference('Jack Admin',$user);

            $manager->persist($user);

        $user = new User();
        $user->setUserName('Jack.Sparrow');
        $user->setEmail('allo_jack@lolmail.com');
        $user->setLanguage("en");
        $user->setStatus(1);
        $user->setPassword($this->encoder->encodePassword(
            $user,
            'userpassword654478'
        ));
            $manager->persist($user);

            $this->setReference('Jack Sparrow',$user);

        $manager->flush();
    }

    public function load_messages(ObjectManager $manager)
    {
        for ($i = 0; $i < 40; $i++) {
            $message = new Message();
            $message->setText('Accumsan in nisl nisi scelerisque eu. Neque laoreet suspendisse interdum consectetur libero. Lorem sed risus ultricies tristique nulla aliquet enim. Id diam maecenas ultricies mi eget mauris. Eget nunc lobortis mattis aliquam faucibus purus in.' . rand(15, 100));
            $message->setStatus(rand(0,1));
            $message->setUser($this->getReference('Jack Sparrow'));
            $manager->persist($message);

        }
        for ($i = 0; $i < 40; $i++) {
            $message = new Message();
            $message->setText('Accumsan in nisl nisi scelerisque eu. Neque laoreet suspendisse interdum consectetur libero. Lorem sed risus ultricies tristique nulla aliquet enim. Id diam maecenas ultricies mi eget mauris. Eget nunc lobortis mattis aliquam faucibus purus in.' . rand(15, 100));
            $message->setStatus(rand(0,1));
            $message->setUser($this->getReference('Jack Admin'));
            $manager->persist($message);

        }

        $manager->flush();


    }
}
