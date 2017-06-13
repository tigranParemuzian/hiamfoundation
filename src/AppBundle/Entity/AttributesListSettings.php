<?php

namespace AppBundle\Entity;

use AppBundle\Model\AttributabeleInterface;
use AppBundle\Traits\AttributesSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributesListSettings
 *
 * @ORM\Table(name="attributes_list_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributesListSettingsRepository")
 */
class AttributesListSettings implements AttributabeleInterface
{
    use AttributesSettings;
    
    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ListValues", inversedBy="settings")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $belongsTo;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AttributesDefinition", cascade={"persist"})
     * @ORM\JoinColumn(name="attributes_definition_id", referencedColumnName="id")
     */
    private $attributesDefinition;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __clone()
    {
        $this->id = null;
        $this->belongsTo = null;
        // TODO: Implement __clone() method.
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
     * Set belongsTo
     *
     * @param \AppBundle\Entity\ListValues $belongsTo
     *
     * @return AttributesListSettings
     */
    public function setBelongsTo(\AppBundle\Entity\ListValues $belongsTo = null)
    {
        $this->belongsTo = $belongsTo;

        return $this;
    }

    /**
     * Get belongsTo
     *
     * @return \AppBundle\Entity\Page
     */
    public function getBelongsTo()
    {
        return $this->belongsTo;
    }

    /**
     * Set attributesDefinition
     *
     * @param \AppBundle\Entity\AttributesDefinition $attributesDefinition
     *
     * @return AttributesListSettings
     */
    public function setAttributesDefinition(\AppBundle\Entity\AttributesDefinition $attributesDefinition = null)
    {
        $this->attributesDefinition = $attributesDefinition;

        return $this;
    }

    /**
     * Get attributesDefinition
     *
     * @return \AppBundle\Entity\AttributesDefinition
     */
    public function getAttributesDefinition()
    {
        return $this->attributesDefinition;
    }
}
