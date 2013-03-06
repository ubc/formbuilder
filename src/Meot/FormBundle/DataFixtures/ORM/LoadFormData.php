<?php
namespace Meot\FormBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Meot\FormBundle\Entity\Form,
    Meot\FormBundle\Entity\FormQuestion;

class LoadFormData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $f1 = new Form();
        $f1->setName('Form 1');
        $f1->setHeader('header 1');
        $f1->setFooter('footer 1');
        $f1->setIsPublic(1);
        $f1->setOwner(1);

        $manager->persist($f1);

        $f2 = new Form();
        $f2->setName('Form 2');
        $f2->setHeader('header 2');
        $f2->setFooter('footer 2');
        $f2->setIsPublic(0);
        $f2->setOwner(1);

        $manager->persist($f2);

        $f3 = new Form();
        $f3->setName('Form 3');
        $f3->setHeader('header 3');
        $f3->setFooter('footer 3');
        $f3->setIsPublic(1);
        $f3->setOwner(1);

        $manager->persist($f3);

        $q1 = new FormQuestion();
        $q1->setForm($f1);
        $q1->setQuestion($this->getReference('q1'));
        $q1->setOrder(1);
        $manager->persist($q1);

        $q2 = new FormQuestion();
        $q2->setForm($f1);
        $q2->setQuestion($this->getReference('q2'));
        $q2->setOrder(2);
        $manager->persist($q2);

        $q3 = new FormQuestion();
        $q3->setForm($f1);
        $q3->setQuestion($this->getReference('q3'));
        $q3->setOrder(3);
        $manager->persist($q3);

        $q4 = new FormQuestion();
        $q4->setForm($f2);
        $q4->setQuestion($this->getReference('q1'));
        $q4->setOrder(1);
        $manager->persist($q4);

        $q5 = new FormQuestion();
        $q5->setForm($f2);
        $q5->setQuestion($this->getReference('q2'));
        $q5->setOrder(5);
        $manager->persist($q5);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}


