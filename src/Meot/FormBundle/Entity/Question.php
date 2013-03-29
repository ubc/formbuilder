<?php

namespace Meot\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy,
    JMS\Serializer\Annotation\ReadOnly,
    JMS\Serializer\Annotation\AccessType,
    JMS\Serializer\Annotation\Type,
    JMS\Serializer\Annotation\Exclude;

/**
 * Question
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ExclusionPolicy("none")
 */
class Question
{
    /**
     * the id of the question
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Question prompt text
     * @var string
     *
     * @ORM\Column(name="question_text", type="text")
     */
    private $text;

    /**
     * Response type
     * @var integer
     *
     * @ORM\Column(name="response_type", type="smallint")
     */
    private $response_type;

    /**
     * @ORM\OneToMany(targetEntity="Response", mappedBy="question", cascade={"persist"})
     */
    private $responses;

    /**
     * if the question is public or private
     * @var boolean
     *
     * @ORM\Column(name="is_public", type="boolean", nullable=true)
     */
    private $is_public = false;

    /**
     * if the question is master question, or otherwise can be edited
     * @var boolean
     *
     * @ORM\Column(name="is_master", type="boolean")
     */
    private $is_master;

    /**
     * the owner id of the question
     * @var integer
     *
     * @ORM\Column(name="owner", type="integer")
     * @ReadOnly
     */
    private $owner;

    /**
     * Question metadata
     * @var string
     *
     * @AccessType("public_method")
     * @Type("array<string, string>")
     * @ORM\Column(name="metadata", type="text", nullable=true)
     */
    private $metadata;

    /**
     * Metadata for response (single line text or textarea)
     * @var string
     *
     * @ORM\Column(name="response_metadata", type="text", nullable=true)
     */
    private $response_metadata;

    /**
     * @ORM\ManyToOne(targetEntity="Form", inversedBy="questions")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     * @Exclude
     */
    private $form;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set response_type
     *
     * @param integer $responseType
     * @return Question
     */
    public function setResponseType($responseType)
    {
        $this->response_type = $responseType;

        return $this;
    }

    /**
     * Get response_type
     *
     * @return integer
     */
    public function getResponseType()
    {
        return $this->response_type;
    }

    /**
     * Set is_public
     *
     * @param boolean $isPublic
     * @return Question
     */
    public function setIsPublic($isPublic)
    {
        $this->is_public = $isPublic;

        return $this;
    }

    /**
     * Get is_public
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->is_public;
    }

    /**
     * Set is_master
     *
     * @param boolean $isMaster
     * @return Question
     */
    public function setIsMaster($isMaster)
    {
        $this->is_master = $isMaster;

        return $this;
    }

    /**
     * Get is_master
     *
     * @return boolean
     */
    public function getIsMaster()
    {
        return $this->is_master;
    }

    /**
     * Set owner
     *
     * @param integer $owner
     * @return Question
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return integer
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add responses
     *
     * @param \Meot\FormBundle\Entity\Response $responses
     * @return Question
     */
    public function addResponse(\Meot\FormBundle\Entity\Response $responses)
    {
        $responses->setQuestion($this);
        $this->responses[] = $responses;

        return $this;
    }

    /**
     * Remove responses
     *
     * @param \Meot\FormBundle\Entity\Response $responses
     */
    public function removeResponse(\Meot\FormBundle\Entity\Response $responses)
    {
        $this->responses->removeElement($responses);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->responses;
    }

    public function __toString()
    {
        return $this->text;
    }

    /**
     * Set metadata
     *
     * @param array $metadata
     * @return Question
     */
    public function setMetadata($metadata)
    {
        if (is_array($metadata)) {
            $tmp = array();
            foreach ($metadata as $key => $value) {
                $tmp[] = $key.':'.$value;
            }
            $this->metadata = implode(';', $tmp);
        } else {
            $this->metadata = $metadata;
        }

        return $this;
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        $tmp = explode(';', $this->metadata);
        $ret = array();

        foreach ($tmp as $element) {
            if (empty($element)) {
                continue;
            }
            $t = explode(':', $element);
            if (count($t) != 2) {
                continue;
            }
            $ret[$t[0]] = $t[1];
        }

        return $ret;
    }

    public function __clone()
    {
        // If the entity has an identity, proceed as normal.
        if ($this->id) {
            // ... Your code here as normal ...
            $oldResponses = $this->responses;
            $this->responses = new ArrayCollection();
            foreach ($oldResponses as $response) {
                $r = clone $response;
                $this->addResponse($r);
            }
        }
        // otherwise do nothing, do NOT throw an exception!
    }

    /**
     * Set form
     *
     * @param \Meot\FormBundle\Entity\Form $form
     * @return Question
     */
    public function setForm(\Meot\FormBundle\Entity\Form $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \Meot\FormBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set response_metadata
     *
     * @param string $responseMetadata
     * @return Question
     */
    public function setResponseMetadata($responseMetadata)
    {
        $this->response_metadata = $responseMetadata;

        return $this;
    }

    /**
     * Get response_metadata
     *
     * @return string
     */
    public function getResponseMetadata()
    {
        return $this->response_metadata;
    }
}
