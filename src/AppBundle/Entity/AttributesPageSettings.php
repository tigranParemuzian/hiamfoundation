<?php

namespace AppBundle\Entity;

use AppBundle\Model\AttributabeleInterface;
use AppBundle\Traits\AttributesSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributesPageSettings
 *
 * @ORM\Table(name="attributes_page_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributesPageSettingsRepository")
 */
class AttributesPageSettings implements AttributabeleInterface
{
    use AttributesSettings;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AttributesDefinition", cascade={"all"})
     * @ORM\JoinColumn(name="attributes_definition_id", referencedColumnName="id")
     */
    private $attributesDefinition;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Page", inversedBy="settings")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $belongsTo;

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
     * @param \AppBundle\Entity\Page $belongsTo
     *
     * @return AttributesPageSettings
     */
    public function setBelongsTo(\AppBundle\Entity\Page $belongsTo = null)
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
     * @return AttributesPageSettings
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
