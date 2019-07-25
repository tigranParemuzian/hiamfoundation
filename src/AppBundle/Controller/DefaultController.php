<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AttributesDefinition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/projects", name="projects")
     * @param Request $request
     */
    public function projectsAction(Request $request){

        return $this->render(':default:projects.html.twig');
    }

    /**
     * @Route("/our-team", name="our-team")
     * @param Request $request
     */
    public function ourTeamAction(Request $request){

        $slug = $request->attributes->get('_route');

        $result = $this->getData($slug);

        return $this->render(':default:our-team.html.twig', ['result'=>$result]);
    }

    /**
     * @Route("/our-team/{slug}", name="our-team-single")
     * @param Request $request
     */
    public function ourTeamSingleAction(Request $request, $slug){

        $em = $this->getDoctrine()->getManager();

        $collect = $em->getRepository('AppBundle:CollectionValues')->findOneBy(['name'=>'Persons']);

        $idsToFilter = [$slug];

        $settingsRm = $collect->getListValues()->filter(


            function($entry) use ($idsToFilter) {
                return in_array($entry->getSlug(), $idsToFilter);
            }
        );

        if(!$settingsRm->isEmpty()){
            $person = $settingsRm->first();
        }else {
            return $this->redirectToRoute('our-team');
        }

        $idsToFilter = [$person->getSortOrdering() - 1, $person->getSortOrdering() + 1 ];

        $settingsRm = $collect->getListValues()->filter(


            function($entry) use ($idsToFilter) {
                return in_array($entry->getSortOrdering(), $idsToFilter);
            }
        );


        if(!$settingsRm->isEmpty()){
            if(count($settingsRm) === 1){
                if($settingsRm->first()->getSortOrdering() < $person->getSortOrdering()){
                    $last = $collect->getListValues()->first()->getSlug();
                    $first = $settingsRm->first()->getSlug();
                }else {
                    $last = $settingsRm->first()->getSlug();
                    $first = $collect->getListValues()->last()->getSlug();
                }
            }else{
                $first = $settingsRm->first()->getSlug();
                $last = $settingsRm->last()->getSlug();
            }
        }

        return $this->render(':default:out-team-single.html.twig', ['person'=>$person, 'first'=>$first, 'last'=>$last]);
    }

    /**
     * @Route("/contact-us", name="contact-us")
     * @param Request $request
     */
    public function contactAction(Request $request){

        return $this->render(':default:contact.html.twig');
    }

    /**
     * @Route("/news", name="news")
     * @param Request $request
     */
    public function newsAction(Request $request){

        return $this->render(':default:news.html.twig');
    }

    /**
     * @Route("/about-us", name="about-us")
     * @param Request $request
     */
    public function aboutUsAction(Request $request){

        return $this->render(':default:about.html.twig');
    }

    /**
     * @Route("/brunches-and-subdivisionsus", name="brunches-and-subdivisions")
     * @param Request $request
     */
    public function brunchesAction(Request $request){

        return $this->render(':default:brunches.html.twig');
    }

    private function getData($slug){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('AppBundle:Page')->findForShow($slug);


        if(!$page){
            return null;
        }

        $data=[];
        foreach ($page->getSettings() as $setting){

            $attr = $setting->getAttributesDefinition();

            $data[$attr->getAttrName()] = $em->getRepository('AppBundle:'.$attr->getAttrClass())->findForViuew(AttributesDefinition::IS_PAGE, $page->getId(), $attr->getAttrName());
        }

        return ['page'=>$page, 'data'=>$data];
    }

}
