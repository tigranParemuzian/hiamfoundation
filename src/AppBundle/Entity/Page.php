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
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class Page implements ConfiguratorInterface
{
    use Configurator;

    const IS_ACTIVE = 0;
    const IS_DRAFT = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AttributesPageSettings", mappedBy="belongsTo", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $settings;

    /**
     * @var
     */
    private $collectionList;

    /**
     * Add value
     *
     * @param \AppBundle\Entity\CollectionValues $collectionList
     *
     * @return $this
     */
    public function addCollectionList(\AppBundle\Entity\CollectionValues $collectionList)
    {
        $this->collectionList[] = $collectionList;

        return $this;
    }

    /**
     * Remove value
     *
     * @param \AppBundle\Entity\CollectionValues $collectionList
     */
    public function removeCollectionList(\AppBundle\Entity\CollectionValues $collectionList)
    {
        $this->collectionList->removeElement($collectionList);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollectionList()
    {
        return $this->values;
    }

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
     * @param \AppBundle\Entity\AttributesPageSettings $setting
     *
     * @return Page
     */
    public function addSetting(\AppBundle\Entity\AttributesPageSettings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\AttributesPageSettings $setting
     */
    public function removeSetting(\AppBundle\Entity\AttributesPageSettings $setting)
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
}
