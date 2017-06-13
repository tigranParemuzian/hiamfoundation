<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListSettingsCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:list_settings_create_command')
            ->setDescription('Hello PhpStorm')
            ->addArgument(
                'intration',
                InputArgument::REQUIRED,
                'type like: BookingController const'
            )
            ->addArgument(
                'cloned_id',
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
        $entity['cloned_id'] = $input->getArgument('cloned_id');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $listFirst = $em->getRepository('AppBundle:ListValues')->find($entity['cloned_id']);

        $settings = $listFirst->getSettings();

        foreach ($listFirst->getCollectionValues()->getListValues() as $list){
            if(count($list->getSettings())==0){

                foreach ($settings as $setting){
                    $newSetting = clone $setting;
                    $newSetting->setBelongsTo($list);
                    $em->persist($newSetting);
                }
            }
        }

        $em->flush();


    }
}
