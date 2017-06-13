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

class CreateDocumentCommand extends ContainerAwareCommand
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
            ->setName('app:create_document_command')
            ->setDescription('Hello PhpStorm')
            ->addArgument(
                'intration',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'attr_class',
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
            )->addArgument(
                'form_type'
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
        $entity['attrClass'] = $input->getArgument('attr_class');
        $entity['belongsToObject'] = $input->getArgument('belongs_to_object');
        $entity['objectName'] = $input->getArgument('object_name');
        $entity['attrName'] = str_replace('_', ' ', $input->getArgument('attr_name'));
        $entity['sortOrdering'] = $input->getArgument('sort_ordering');
        $entity['formType'] = $input->getArgument('form_type');

        $this->log = $this->getContainer()->get('monolog.logger.process_error');
        $logInfo = $this->getContainer()->get('monolog.logger.command_create');

        $message = json_encode($entity);
        $logInfo->addInfo("Request create_document_command : $message\r\n");


        if($entity['objectName'] == AttributesDefinition::IS_LIST) {
            $belongsToObject = $em->getRepository('AppBundle:ListValues')->find($entity['belongsToObject']);
        }else {
            $belongsToObject = null;
        }
        sleep(2);
        switch ($entity['attrClass']){
                case AttributesDefinition::IS_LIST:
                    $this->createListValues($entity);
                    break;
                case AttributesDefinition::IS_COLLECTION;
                    $collection = $this->createCollection($entity);
                    $this->createListValues($entity, $collection);
                    break;
                case AttributesDefinition::IS_TEXT;
                    $text = $this->createText($entity, $belongsToObject, $entity['formType']);
                    break;
                case AttributesDefinition::IS_IMAGE;
                    $image = $this->createImage($entity, $belongsToObject);
                    break;
                case AttributesDefinition::IS_FILE;
                    $file = $this->createFile($entity, $belongsToObject);
                    break;
                case AttributesDefinition::IS_DATE;
                    $date = $this->createDate($entity, $belongsToObject);
                    break;
                case AttributesDefinition::IS_BOOL;
                    $bool = $this->createBoolean($entity, $belongsToObject);
                    break;

                default;
                    break;
            }
        $em->flush();


        $output->writeln("<info>Finish </info>");

    }

    private function createListValues($object, $collection =null){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $listItem = new ListValues();
        $listItem->setBelongsToObject((int)$object['belongsToObject']);
        $listItem->setBelongsToObjectName($collection ? AttributesDefinition::IS_COLLECTION : $object['objectName']);
        $listItem->setName($object['attrName']);
        $listItem->setCollectionValues($collection);
        $listItem->setSortOrdering(1);

        $errors = $this->validator->validate($listItem);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            $this->log->addInfo("Request ListValues create : $errorsString\r\n");
        }else{

            $em->persist($listItem);
        }


    }

    private function createCollection($object){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $collection = new CollectionValues();
        $collection->setBelongsToObject($object['belongsToObject']);
        $collection->setBelongsToObjectName($object['objectName']);
        $collection->setName($object['attrName']);

        $errors = $this->validator->validate($collection);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            $this->log->addInfo("Request CollectionValues create : $errorsString\r\n");
        }else{

            $em->persist($collection);
        }

        return $collection;
    }

    private function createText($object, $belongsToObject, $formType = null){

        $em = $this->getContainer()->get('doctrine')->getManager();

            $text = new TextValues();
            $text->setBelongsToObject($belongsToObject);
            $text->setTitle($object['attrName']);
            $text->setValue(' ');
            $text->setFormType($formType);
            $text->setSortOrdering($object['sortOrdering']);

        $errors = $this->validator->validate($text);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $this->log->addInfo("Request Text create : $errorsString\r\n");
        }else{

            $em->persist($text);
        }
    }

    private function createBoolean($object, $belongsToObject){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $text = new BooleanValues();
        $text->setBelongsToObject($belongsToObject);
        $text->setTitle($object['attrName']);
        $text->setValue(true);
        $text->setSortOrdering($object['sortOrdering']);

        $errors = $this->validator->validate($text);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $this->log->addInfo("Request Boolean create : $errorsString\r\n");
        }else{

            $em->persist($text);
        }
    }

    private function createImage($object, $belongsToObject){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $image = new Image();
        $image->setBelongsToObject($belongsToObject);
        $image->setTitle($object['attrName']);
        $image->setSortOrdering($object['sortOrdering']);

        $errors = $this->validator->validate($image);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $this->log->addInfo("Request Image create : $errorsString\r\n");
        }else{

            $em->persist($image);
        }
    }

    private function createFile($object, $belongsToObject){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $image = new File();
        $image->setBelongsToObject($belongsToObject);
        $image->setTitle($object['attrName']);
        $image->setSortOrdering($object['sortOrdering']);

        $errors = $this->validator->validate($image);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $this->log->addInfo("Request File create : $errorsString\r\n");
        }else{

            $em->persist($image);
        }
    }

    private function createDate($object, $belongsToObject){

        $em = $this->getContainer()->get('doctrine')->getManager();

        $date = new DateValues();
        $date->setBelongsToObject($belongsToObject);
        $date->setTitle($object['attrName']);
        $date->setValue(new \DateTime('now'));
        $date->setSortOrdering($object['sortOrdering']);

        $errors = $this->validator->validate($date);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            $this->log->addInfo("Request Date create : $errorsString\r\n");
        }else{

            $em->persist($date);
        }
    }
}
