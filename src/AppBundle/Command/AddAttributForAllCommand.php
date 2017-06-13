<?php

namespace AppBundle\Command;

use AppBundle\Entity\AttributesDefinition;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddAttributForAllCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:add_attribut_for_all_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $abouts = $em->getRepository('AppBundle:ListValues')->findBy(['label'=>'About']);

        $count = count($abouts);
        $output->writeln("<info>Starting create Date $count </info>");
        $i = 0;
        foreach ($abouts as $about){

            $attrDeff = new AttributesDefinition();
            $attrDeff->setObjectName(AttributesDefinition::IS_LIST);
            $attrDeff->setIsPublic(true);
            $attrDeff->setBelongsToObject($about->getId());
            $attrDeff->setModeratorSortOrder(10);
            $attrDeff->setAttrName('Started Date');
            $attrDeff->setAttrClass(AttributesDefinition::IS_DATE);
            $em->persist($attrDeff);

            $attrDeffEnd = new AttributesDefinition();
            $attrDeffEnd->setObjectName(AttributesDefinition::IS_LIST);
            $attrDeffEnd->setIsPublic(true);
            $attrDeffEnd->setBelongsToObject($about->getId());
            $attrDeffEnd->setModeratorSortOrder(10);
            $attrDeffEnd->setAttrName('End Date');
            $attrDeffEnd->setAttrClass(AttributesDefinition::IS_DATE);
            $em->persist($attrDeffEnd);

            $need = $count-$i;
            $output->writeln("<info>Starting create Date for {$about->getLabel()} inter number {$i} Need update {$need} </info>");
            $i++;
            sleep(10);
            if($i%3==0){
                $em->flush();
            }
        }

        $em->flush();

        $output->writeln("<info>Finished</info>");
    }

}
