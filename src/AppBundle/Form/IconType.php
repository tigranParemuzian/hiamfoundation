<?php
namespace AppBundle\Form;
use AppBundle\Entity\MenuItom;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class IconType extends AbstractType
{
    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'file';
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'icon_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( array(
            'belongs_to'=>false
        ));
    }
}