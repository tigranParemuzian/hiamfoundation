<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AttributesDefinition
 *
 * @ORM\Table(name="attributes_definition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributesDefinitionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AttributesDefinition
{
    const IS_TEXT = 'TextValues';
    const IS_BOOL = 'BooleanValues';
    const IS_IMAGE = 'Image';
    const IS_FILE = 'File';
    const IS_LIST = 'ListValues';
    const IS_DATE = 'DateValues';
    const IS_COLLECTION = 'CollectionValues';

    const IS_CAMPAIGN = 'Campaign';
    const IS_PROJECT = 'Project';
    const IS_PAGE = 'Page';
    const IS_LIST_OBJECT = 'ListValues';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"attrName"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="attr_name", type="string", length=255)
     */
    private $attrName;

    /**
     * @var string
     *
     * @ORM\Column(name="attr_class", type="string", length=255)
     */
    private $attrClass;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    private $isRequired;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $isPublic;

    /**
     * @var
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    public function __clone()
    {
        $this->id = null;
    }

    public function __toString()
    {
        return $this->id ? $this->attrName : 'new Attr Def';
        // TODO: Implement __toString() method.
    }

    public function __construct()
    {
//        $this->isPublic = true;
        $this->isRequired= false;
        $this->status= false;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set attrName
     *
     * @param string $attrName
     *
     * @return AttributesDefinition
     */
    public function setAttrName($attrName)
    {
        $this->attrName = $attrName;

        return $this;
    }

    /**
     * Get attrName
     *
     * @return string
     */
    public function getAttrName()
    {
        return $this->attrName;
    }

    /**
     * Set attrClass
     *
     * @param string $attrClass
     *
     * @return AttributesDefinition
     */
    public function setAttrClass($attrClass)
    {
        $this->attrClass = $attrClass;

        return $this;
    }

    /**
     * Get attrClass
     *
     * @return string
     */
    public function getAttrClass()
    {
        return $this->attrClass;
    }

    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     *
     * @return AttributesDefinition
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
