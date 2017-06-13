<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CollectionValues
 *
 * @ORM\Table(name="collection_values")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CollectionValuesRepository")
 */
class CollectionValues
{
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="belongs_to_object_name", type="string", length=50)
     */
    private $belongsToObjectName;

    /**
     * @var string
     *
     * @ORM\Column(name="belongs_to_object", type="integer")
     */
    private $belongsToObject;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ListValues", mappedBy="collectionValues", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $listValues;


    public function __clone()
    {
        $this->id = null;
        $this->belongsToObject = null;

        /*$values = $this->getListValues();
        $this->listValues = new ArrayCollection();
        if (count($values) > 0) {
            foreach ($values as $value) {
                $cloneValues = clone $value;
                $this->listValues->add($cloneValues);
                $cloneValues->setBelongsToObject($this);
            }
        }*/
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

    public function __toString()
    {
        return $this->id ? $this->name : 'New Collection';
        // TODO: Implement __toString() method.
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listValues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CollectionValues
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
     * Set belongsToObjectName
     *
     * @param string $belongsToObjectName
     *
     * @return CollectionValues
     */
    public function setBelongsToObjectName($belongsToObjectName)
    {
        $this->belongsToObjectName = $belongsToObjectName;

        return $this;
    }

    /**
     * Get belongsToObjectName
     *
     * @return string
     */
    public function getBelongsToObjectName()
    {
        return $this->belongsToObjectName;
    }

    /**
     * Set belongsToObject
     *
     * @param integer $belongsToObject
     *
     * @return CollectionValues
     */
    public function setBelongsToObject($belongsToObject)
    {
        $this->belongsToObject = $belongsToObject;

        return $this;
    }

    /**
     * Get belongsToObject
     *
     * @return integer
     */
    public function getBelongsToObject()
    {
        return $this->belongsToObject;
    }

    /**
     * Add listValue
     *
     * @param \AppBundle\Entity\ListValues $listValue
     *
     * @return CollectionValues
     */
    public function addListValue(\AppBundle\Entity\ListValues $listValue)
    {
        $this->listValues[] = $listValue;

        return $this;
    }

    /**
     * Remove listValue
     *
     * @param \AppBundle\Entity\ListValues $listValue
     */
    public function removeListValue(\AppBundle\Entity\ListValues $listValue)
    {
        $this->listValues->removeElement($listValue);
    }

    /**
     * Get listValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListValues()
    {
        return $this->listValues;
    }
}
