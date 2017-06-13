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
trait AttributesSettings
{

    /**
     * @var int
     *
     * @ORM\Column(name="sort_ordering", type="integer")
     * @Assert\NotBlank(message="Sort Ordering can`t be null")
     */
    private $sortOrdering;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_enable", type="boolean")
     * @Assert\NotBlank(message="Is Enable can`t be null")
     */
    private $isEnable;

    public function __construct()
    {
        $this->isEnable = true;
    }

    /**
     * @param $sortOrdering
     * @return $this
     */
    public function setSortOrdering($sortOrdering)
    {
        $this->sortOrdering = $sortOrdering;

        return $this;
    }

    /**
     * Get sortOrdering
     *
     * @return int
     */
    public function getSortOrdering()
    {
        return $this->sortOrdering;
    }


    public function setIsEnable($isEnable)
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    /**
     * Get isEnable
     *
     * @return bool
     */
    public function getIsEnable()
    {
        return $this->isEnable;
    }

}