<?php

namespace AppBundle\Entity;

use AppBundle\Model\AttributeInterface;
use AppBundle\Traits\Attributes;
use Doctrine\ORM\Mapping as ORM;
use \AppBundle\Traits\File as FileTrade;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FileRepository")
 */
class File implements AttributeInterface
{
    use FileTrade, Attributes;

    const IS_PDF = 0;
    const IS_DOC = 1;

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
     * @ORM\Column(name="state", type="smallint")
     */
    private $state;

    public function __construct()
    {
        $this->state = self::IS_PDF;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return File
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }
}
