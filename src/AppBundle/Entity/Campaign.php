<?php

namespace AppBundle\Entity;

use AppBundle\Model\ConfiguratorInterface;
use AppBundle\Traits\Configurator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Campaign
 *
 * @ORM\Table(name="campaign")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CampaignRepository")
 */
class Campaign implements ConfiguratorInterface
{
    use Configurator;

    const IS_ACTIVE = 0;
    const IS_DRAFT = 1;
    const IS_COMPLETED = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AttributesCampaignSettings", mappedBy="belongsTo", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $settings;

    /**
     * @var
     * @ORM\Column(name="sort_order_date", type="datetime", nullable=true)
     */
    private $sortOrderDate;

    public function __clone()
    {
        $this->id = null;

        $settings = $this->getSettings();
        $this->settings = new ArrayCollection();
        if(count($settings) > 0){
            foreach ($settings as $setting) {
                $cloneSetting = clone $setting;
                $this->settings->add($cloneSetting);
                $cloneSetting->setBelongsTo($this);
            }
        }
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
     * Add setting
     *
     * @param \AppBundle\Entity\AttributesCampaignSettings $setting
     *
     * @return Campaign
     */
    public function addSetting(\AppBundle\Entity\AttributesCampaignSettings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\AttributesCampaignSettings $setting
     */
    public function removeSetting(\AppBundle\Entity\AttributesCampaignSettings $setting)
    {
        $this->settings->removeElement($setting);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return mixed
     */
    public function getSortOrderDate()
    {
        return $this->sortOrderDate;
    }

    /**
     * @param mixed $sortOrderDate
     */
    public function setSortOrderDate($sortOrderDate)
    {
        $this->sortOrderDate = $sortOrderDate;
    }
}
