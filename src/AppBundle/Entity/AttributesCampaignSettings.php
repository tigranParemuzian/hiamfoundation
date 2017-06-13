<?php

namespace AppBundle\Entity;

use AppBundle\Model\AttributabeleInterface;
use AppBundle\Traits\AttributesSettings;
use Doctrine\ORM\Mapping as ORM;

/**
 * AttributesCampaignSettings
 *
 * @ORM\Table(name="attributes_campaign_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AttributesCampaignSettingsRepository")
 */
class AttributesCampaignSettings implements AttributabeleInterface
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
     * @ORM\ManyToOne(targetEntity="AttributesDefinition", cascade={"persist"})
     * @ORM\JoinColumn(name="attributes_definition_id", referencedColumnName="id")
     */
    private $attributesDefinition;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="settings", cascade={"persist"})
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @param \AppBundle\Entity\Campaign $belongsTo
     *
     * @return AttributesCampaignSettings
     */
    public function setBelongsTo(\AppBundle\Entity\Campaign $belongsTo = null)
    {
        $this->belongsTo = $belongsTo;

        return $this;
    }

    /**
     * Get belongsTo
     *
     * @return \AppBundle\Entity\Campaign
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
