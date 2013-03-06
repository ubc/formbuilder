<?php
namespace Meot\FormBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Meot\FormBundle\Entity\Question,
    Meot\FormBundle\Entity\Response;

class LoadQuestionData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $q1 = new Question();
        $q1->setText('Question 1');
        $q1->setResponseType(1);
        $q1->setIsPublic(1);
        $q1->setIsMaster(1);
        $q1->setOwner(1);

        $manager->persist($q1);

        $q2 = new Question();
        $q2->setText('Question 2');
        $q2->setResponseType(2);
        $q2->setIsPublic(0);
        $q2->setIsMaster(1);
        $q2->setOwner(1);

        $manager->persist($q2);

        $q3 = new Question();
        $q3->setText('Question 3');
        $q3->setResponseType(3);
        $q3->setIsPublic(1);
        $q3->setIsMaster(0);
        $q3->setOwner(1);

        $manager->persist($q3);

        $r1 = new Response();
        $r1->setQuestion($q1);
        $r1->setText('Response 1');
        $manager->persist($r1);

        $r2 = new Response();
        $r2->setQuestion($q1);
        $r2->setText('Response 2');
        $manager->persist($r2);

        $r3 = new Response();
        $r3->setQuestion($q1);
        $r3->setText('Response 3');
        $manager->persist($r3);

        $r4 = new Response();
        $r4->setQuestion($q2);
        $r4->setText('Response 4');
        $manager->persist($r4);

        $r5 = new Response();
        $r5->setQuestion($q2);
        $r5->setText('Response 5');
        $manager->persist($r5);

        $manager->flush();
    }
}

