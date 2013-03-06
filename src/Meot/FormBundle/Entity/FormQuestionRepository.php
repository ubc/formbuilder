<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FormQuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormQuestionRepository extends EntityRepository
{
    public function getByFormAndQuestion($form_id, $question_id)
    {
        return $this->findOneBy(array('form_id' => $form_id, 'question_id' => $question_id));
    }
}
