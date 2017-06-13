<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListingOrderCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:listing_order_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();


/*        $timelines = $em->getRepository('AppBundle:CollectionValues')->findByLagelTimeline(['label'=>'Timeline']);

        $dates = [];
        foreach ($timelines AS $timeline){
            foreach ($timeline->getListValues() as $listValue){

                $current = $listValue->getDate()->first();
                $dates[$listValue->getId()] = $current->getValue();

            }
        }

        $em->createQueryBuilder()
            ->update('AppBundle:AttributesCampaignSettings', 'ad')
            ->set('ad.sortOrdering', ':ord')
            ->where('ad.id = :id')
            ->setParameter('id', $id)
            ->setParameter('ord', $odred)
            ->getQuery()->execute()
        ;*/

        $classes = ['AttributesCampaignSettings', 'AttributesProjectSettings'];

        foreach ($classes as $class){

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Header');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(1);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('About');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(2);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Timeline');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(3);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Sidebar');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(4);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Fundraiser ideas');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(5);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Fundraiser Ideas Button');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(6);
                    $em->persist($timeline);
                }
            }

            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Sidebar Title');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(7);
                    $em->persist($timeline);
                }
            }


            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Contributors Image');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(8);
                    $em->persist($timeline);
                }
            }
            $timelines = $em->getRepository('AppBundle:'.$class)->updateOrdering('Classy Campaign Id');

            if($timelines){

                foreach ($timelines as $timeline){
                    $timeline->setSortOrdering(9);
                    $em->persist($timeline);
                }
            }

        }
        
        $em->flush();
    }
}
