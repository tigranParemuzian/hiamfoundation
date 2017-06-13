<?php

namespace AppBundle\Entity;

use AppBundle\Model\ConfiguratorInterface;
use AppBundle\Traits\Configurator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ListValues
 *
 * @ORM\Table(name="list_values")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ListValuesRepository")
 */
class ListValues implements ConfiguratorInterface
{
    use Configurator;

    const IS_CAMPAIGN = 'Campaign';
    const IS_PROJECT = 'Project';
    const IS_PAGE = 'Page';

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
     * @ORM\Column(name="belongs_to_object_name", type="string", length=50)
     */
    private $belongsToObjectName;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_ordering", type="integer")
     * @Assert\NotBlank(message="Sort Ordering can`t be null")
     */
    private $sortOrdering;

    /**
     * @var string
     *
     * @ORM\Column(name="belongs_to_object", type="integer")
     */
    private $belongsToObject;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AttributesListSettings", mappedBy="belongsTo", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $settings;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Image", mappedBy="belongsToObject", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $image;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\File", mappedBy="belongsToObject", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $file;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TextValues", mappedBy="belongsToObject", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $text;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\DateValues", mappedBy="belongsToObject", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $date;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BooleanValues", mappedBy="belongsToObject", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"sortOrdering"="ASC"})
     */
    private $boolean;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CollectionValues", inversedBy="listValues")
     * @ORM\JoinColumn(name="collection_values_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $collectionValues;

    /**
     * @Serializer\VirtualProperty()
     */
    public function getActualId(){
        return $this->id;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function setActualId($id){
        return '';
    }

    public function __clone()
    {
        $this->id = null;
        $this->belongsToObject = null;

        $images = $this->getImage();
        $this->image = new ArrayCollection();
        if(count($images) > 0){
            foreach ($images as $image) {
                $cloneImagen = clone $image;
                $this->image->add($cloneImagen);
                $cloneImagen->setBelongsToObject($this);
            }
        }

        $files = $this->getFile();
        $this->file = new ArrayCollection();
        if(count($files) > 0){
            foreach ($files as $file) {
                $cloneFile = clone $file;
                $this->file->add($cloneFile);
                $cloneFile->setBelongsToObject($this);
            }
        }

        $textes = $this->getText();
        $this->text = new ArrayCollection();
        if(count($textes) > 0){
            foreach ($textes as $text) {
                $cloneText = clone $text;
                $this->text->add($cloneText);
                $cloneText->setBelongsToObject($this);
            }
        }

        $booleans = $this->getBoolean();
        $this->boolean = new ArrayCollection();
        if(count($booleans) > 0){
            foreach ($booleans as $boolean) {
                $cloneBoolean = clone $boolean;
                $this->boolean->add($cloneBoolean);
                $cloneBoolean->setBelongsToObject($this);
            }
        }

        $dates = $this->getDate();
        $this->date = new ArrayCollection();
        if(count($dates) > 0){
            foreach ($dates as $date) {
                $cloneDate = clone $date;
                $this->date->add($cloneDate);
                $cloneDate->setBelongsToObject($this);
            }
        }

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

    public function __toString()
    {
       return $this->id ? $this->name : 'New Listing';
        // TODO: Implement __toString() method.
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->state = true;
        $this->settings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->image = new \Doctrine\Common\Collections\ArrayCollection();
        $this->file = new \Doctrine\Common\Collections\ArrayCollection();
        $this->text = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \Doctrine\Common\Collections\ArrayCollection();
        $this->boolean = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set belongsToObjectName
     *
     * @param string $belongsToObjectName
     *
     * @return ListValues
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
     * @return ListValues
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
     * Add image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return ListValues
     */
    public function addImage(\AppBundle\Entity\Image $image)
    {
        $this->image[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\Image $image
     */
    public function removeImage(\AppBundle\Entity\Image $image)
    {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add file
     *
     * @param \AppBundle\Entity\File $file
     *
     * @return ListValues
     */
    public function addFile(\AppBundle\Entity\File $file)
    {
        $this->file[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \AppBundle\Entity\File $file
     */
    public function removeFile(\AppBundle\Entity\File $file)
    {
        $this->file->removeElement($file);
    }

    /**
     * Get file
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Add text
     *
     * @param \AppBundle\Entity\TextValues $text
     *
     * @return ListValues
     */
    public function addText(\AppBundle\Entity\TextValues $text)
    {
        $this->text[] = $text;

        return $this;
    }

    /**
     * Remove text
     *
     * @param \AppBundle\Entity\TextValues $text
     */
    public function removeText(\AppBundle\Entity\TextValues $text)
    {
        $this->text->removeElement($text);
    }

    /**
     * Get text
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Add date
     *
     * @param \AppBundle\Entity\DateValues $date
     *
     * @return ListValues
     */
    public function addDate(\AppBundle\Entity\DateValues $date)
    {
        $this->date[] = $date;

        return $this;
    }

    /**
     * Remove date
     *
     * @param \AppBundle\Entity\DateValues $date
     */
    public function removeDate(\AppBundle\Entity\DateValues $date)
    {
        $this->date->removeElement($date);
    }

    /**
     * Get date
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set collectionValues
     *
     * @param \AppBundle\Entity\CollectionValues $collectionValues
     *
     * @return ListValues
     */
    public function setCollectionValues(\AppBundle\Entity\CollectionValues $collectionValues = null)
    {
        $this->collectionValues = $collectionValues;

        return $this;
    }

    /**
     * Get collectionValues
     *
     * @return \AppBundle\Entity\CollectionValues
     */
    public function getCollectionValues()
    {
        return $this->collectionValues;
    }

    /**
     * Add boolean
     *
     * @param \AppBundle\Entity\BooleanValues $boolean
     *
     * @return ListValues
     */
    public function addBoolean(\AppBundle\Entity\BooleanValues $boolean)
    {
        $this->boolean[] = $boolean;

        return $this;
    }

    /**
     * Remove boolean
     *
     * @param \AppBundle\Entity\BooleanValues $boolean
     */
    public function removeBoolean(\AppBundle\Entity\BooleanValues $boolean)
    {
        $this->boolean->removeElement($boolean);
    }

    /**
     * Get boolean
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * Add setting
     *
     * @param \AppBundle\Entity\AttributesListSettings $setting
     *
     * @return ListValues
     */
    public function addSetting(\AppBundle\Entity\AttributesListSettings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\AttributesListSettings $setting
     */
    public function removeSetting(\AppBundle\Entity\AttributesListSettings $setting)
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
     * @return int
     */
    public function getSortOrdering()
    {
        return $this->sortOrdering;
    }

    /**
     * @param int $sortOrdering
     */
    public function setSortOrdering($sortOrdering)
    {
        $this->sortOrdering = $sortOrdering;
    }
}
