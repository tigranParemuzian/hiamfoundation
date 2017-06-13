<?php

namespace AppBundle\Command;

use AppBundle\Entity\DateValues;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutDatesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:about_dates_command')
            ->setDescription('Update About Dates');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();


        $listValues = $em->getRepository('AppBundle:ListValues')->findBy(['label'=>'About', 'belongsToObjectName'=>'Project']);

        foreach ($listValues as $listValue){

            if(count($listValue->getDate()) != 2){
                $idsToFilter = array('Started Date', 'End Date');

                $dates = $listValue->getDate()->filter(

                    function($entry) use ($idsToFilter) {
                        return in_array($entry->getTitle(), $idsToFilter);
                    }
                );
                $newDate = clone $dates->first();
                if($dates->first()->getTitle() == 'End Date'){

                    $newDate->setTitle('Started Date');

                }else{
                    $newDate->setTitle('End Date');
                }
                $newDate->setValue(new \DateTime('now'));
                $em->persist($newDate);
            }
        }
        $em->flush();


    }
}
