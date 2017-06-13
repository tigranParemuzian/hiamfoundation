<?php

namespace AppBundle\Form;

use AppBundle\Entity\AttributesDefinition;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignValuesType extends AbstractType
{
    private $em;
    private $settings;

    public function __construct(EntityManager $entityManager, $settings = array())
    {
        $this->em = $entityManager;
        $settings ? $this->settings = $settings : $this->settings = [];
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settings as $setting){

            switch ($setting->getAttrClass()){

                case AttributesDefinition::IS_IMAGE:
                    $builder
                        ->add('image'.$setting->getSlug(), 'icon_type', ['label'=>'image', 'required'=>$setting->getIsRequired()]);
                    break;
                case AttributesDefinition::IS_FILE:
                    $builder
                        ->add('file'.$setting->getSlug(), 'icon_type', ['label'=>'File', 'required'=>$setting->getIsRequired()]);
                    break;
                case AttributesDefinition::IS_TEXT:
                    $builder
                        ->add('text'.$setting->getSlug(), 'text', ['label'=>'insert Text', 'required'=>$setting->getIsRequired()]);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'campaign_values_type';
    }
}
