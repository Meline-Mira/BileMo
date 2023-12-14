<?php

namespace App\DataFixtures;

use App\Entity\ClientUser;
use App\Entity\Phone;
use App\Entity\Picture;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $phone = new Phone;
            $phone->setName('Téléphone numéro ' . $i);
            $phone->setDescription($faker->text());
            $phone->setPrice(1649.99);
            $manager->persist($phone);

            for ($j = 0; $j < 5; $j++) {
                $picture = new Picture;
                $picture->setUrl($faker->url());
                $picture->setDescritpion($faker->text($faker->numberBetween(10, 100)));
                $picture->setPhone($phone);
                $manager->persist($picture);
            }
        }

        for ($k = 0; $k < 3; $k++) {
            $user = new User;
            $user->setUsername($faker->name());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $manager->persist($user);

            for ($l = 0; $l < 10; $l++) {
                $clientUser = new ClientUser;
                $clientUser->setFirstName($firstName = $faker->firstName());
                $clientUser->setLastName($lastName = $faker->lastName());
                $clientUser->setEmail(strtolower($firstName.'.'.$lastName.'@'.$faker->domainName()));
                $clientUser->setUser($user);
                $manager->persist($clientUser);
            }
        }

        $manager->flush();
    }
}
