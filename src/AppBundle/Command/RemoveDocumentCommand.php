<?php

namespace AppBundle\Command;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\BooleanValues;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\DateValues;
use AppBundle\Entity\File;
use AppBundle\Entity\Image;
use AppBundle\Entity\ListValues;
use AppBundle\Entity\TextValues;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDocumentCommand extends ContainerAwareCommand
{

    protected $log;
    protected $em;
    protected $validator;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:remove_document_command')
            ->setDescription('This command use to remove documents or documents list by belongs to Configurator name and Id')
            ->addArgument(
                'intration',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'belongs_to_object',
                InputArgument::REQUIRED,
                'data json'
            )
            ->addArgument(
                'object_name'
            /* InputArgument::REQUIRED,
             'data json'*/
            )
            ->addArgument(
                'attr_name'
            )
            ->addArgument(
                'sort_ordering'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();

        $this->validator = $this->getContainer()->get('validator');

        $output->writeln("<info>Starting send push note </info>");
        $entity['belongsToObject'] = $input->getArgument('belongs_to_object');
        $entity['objectName'] = $input->getArgument('object_name');

        $this->log = $this->getContainer()->get('monolog.logger.process_error');
        $logInfo = $this->getContainer()->get('monolog.logger.command_create');

        $message = json_encode($entity);
        $logInfo->addInfo("Request create_document_command : $message\r\n");


        $listValues = $em->getRepository('AppBundle:ListValues')->findBy(['belongsToObjectName'=>$entity['objectName'],
            'belongsToObject'=>(int)$entity['belongsToObject']]);

        foreach ($listValues as $listValue){
            $em->remove($listValue);
        }
        $collectionValues = $em->getRepository('AppBundle:CollectionValues')->findBy(['belongsToObjectName'=>$entity['objectName'],
            'belongsToObject'=>(int)$entity['belongsToObject']]);

        foreach ($collectionValues as $collectionValue){
            $em->remove($collectionValue);
        }

        $em->flush();
        $output->writeln("<info>Finish </info>");

    }
}
