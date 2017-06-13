<?php

namespace AppBundle\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * This traits is used for include file
 *
 * Class File
 * @package AppBundle\Traits
 */
trait Configurator
{

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Name can`t be null")
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="temp_slug", type="string", length=255, unique=false, nullable=true)
     */
    private $tempSlug;

    /**
     * @var
     * @ORM\Column(name="state", type="smallint", nullable=true, options={"default":0})
     */
    private $state;

    /**
     * @var
     * @ORM\Column(name="version", type="integer", nullable=true, options={"default":1})
     */
    private $version;

    /**
     * @var
     */
    private $values;

    /**
     * @var
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * Add value
     *
     * @param \AppBundle\Entity\ListValues $value
     *
     * @return $this
     */
    public function addValue(\AppBundle\Entity\ListValues $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove value
     *
     * @param \AppBundle\Entity\ListValues $value
     */
    public function removeValue(\AppBundle\Entity\ListValues $value)
    {
        $this->values->removeElement($value);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * This function is used to get object class name
     *
     * @return string
     */
    public function getClassName(){
        return get_class($this);
    }

    public function __toString()
    {
        return $this->id ? $this->name : 'New record';
        // TODO: Implement __toString() method.
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->state = self::IS_ACTIVE;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return string
     */
    public function getTempSlug()
    {
        return $this->tempSlug;
    }

    /**
     * @param string $tempSlug
     */
    public function setTempSlug($tempSlug)
    {
        $this->tempSlug = $tempSlug;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}