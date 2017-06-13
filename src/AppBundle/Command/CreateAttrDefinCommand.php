<?php

namespace AppBundle\Command;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\ListValues;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAttrDefinCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:create_attr_defin_command')
            ->setDescription('Hello PhpStorm')
            ->addArgument(
                'intration',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'arrt_id',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'list_val_id',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'obj_name',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )->addArgument(
                'label',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln("<info>Starting send push note </info>");
        $entity['intration'] = $input->getArgument('intration');
        $entity['arrtId'] = $input->getArgument('arrt_id');
        $entity['list_val_slug'] = $input->getArgument('list_val_id');
        $entity['obj_name'] = $input->getArgument('obj_name');
        $entity['label'] = $input->getArgument('label');

        $logger = $this->getContainer()->get('monolog.logger.command_create');

        $message = json_encode($entity);
        $logger->addInfo("Request create_attr_defin_command : $message\r\n");

        $attr = $em->getRepository('AppBundle:AttributesDefinition')->find($entity['arrtId']);
        $list = $em->getRepository('AppBundle:'.$entity['obj_name'])->findOneBySlug($entity['list_val_slug']);

         if(!$list){
             sleep(2);
             $list = $em->getRepository('AppBundle:'.$entity['obj_name'])->findOneBySlug($entity['list_val_slug']);
         }


        $logger->addInfo("Request create_attr_defin_command : {$list->getId()}\r\n");

        $new = clone  $attr;
        $new->setAttrName($entity['label']);
        $new->setBelongsToObject($list->getId());
        $em->persist($new);

        $em->flush();

        $output->writeln("<info>Finish </info>");
    }
}
