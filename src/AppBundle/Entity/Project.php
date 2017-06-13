<?php

namespace AppBundle\Entity;

use AppBundle\Model\ConfiguratorInterface;
use AppBundle\Traits\Configurator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project implements ConfiguratorInterface
{
    use Configurator;

    const IS_ACTIVE = 0;
    const IS_DRAFT = 1;
    const IS_COMPLETED = 2;

    const IS_AGRICULTURE = 3;
    const IS_TECHNOLOGY = 4;
    const IS_TOURISM = 5;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AttributesProjectSettings", mappedBy="belongsTo", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $settings;

    /**
     * @var
     * @ORM\Column(name="rest", type="float", nullable=true)
     */
    private $rest;

    /**
     * @var
     * @ORM\Column(name="goal", type="float", nullable=true)
     */
    private $goal;

    /**
     * @var int
     * @ORM\Column(name="category", type="smallint", nullable=true)
     *
     */
    private $category;

    /**
     * @var
     * @ORM\Column(name="sort_order_date", type="datetime", nullable=true)
     */
    private $sortOrderDate;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Add setting
     *
     * @param \AppBundle\Entity\AttributesProjectSettings $setting
     *
     * @return Project
     */
    public function addSetting(\AppBundle\Entity\AttributesProjectSettings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\AttributesProjectSettings $setting
     */
    public function removeSetting(\AppBundle\Entity\AttributesProjectSettings $setting)
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
    public function getRest()
    {
        return $this->rest;
    }

    /**
     * @param mixed $rest
     */
    public function setRest($rest)
    {
        $this->rest = $rest;
    }

    /**
     * @return mixed
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * @param mixed $goal
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
    }

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
