<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\ListValues;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookingController
 *
 * @package AppBundle\Controller\Rest
 *
 * @RouteResource("Campaign")
 * @Rest\Prefix("/api/campaign")
 */
class CampaignController extends FOSRestController
{

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Campaign",
     *  description="This function is used to get all new bookings.",
     *  statusCodes={
     *         202="Returned when find",
     *         404="Return when new bookings not found", }
     * )
     *
     * This function is used to get all new bookings
     * @Rest\View()
     *
     */
    public function getAction($objectName, $belongsToObject, $attrName, $attrClass){

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:'.$attrClass)->findForViuew($objectName, $belongsToObject, $attrName);
        $twigPrefix = str_replace(' ', '_', strtolower($attrName));

        $methods = get_class_methods(new ListValues());
        $dataValues = null;
        $rend = true;
        $isList = false;
        $emptyList = [];
        $infos = [];
        $entry = [];
        foreach ($data as $object){

            if($object instanceof ListValues){
                $dataValues = $object;
                $isList = true;
            }

        }

        $message = json_encode($entry);
        $logger = $this->container->get('monolog.logger.process_error');
        $logger->addInfo("Request info Templateing: $message\r\n");

        if($isList == true){
            $data = $dataValues;
        }

        /*if($twigPrefix == 'header'*/ /*|| $twigPrefix == 'about' || $twigPrefix == 'timeline' || $twigPrefix == 'contributors_image' || $twigPrefix == 'sidebar'*//*){*/
            $view = $this->view($data, 200)
                ->setTemplate("AppBundle:Campaign:" . $twigPrefix . ".html.twig")
                ->setTemplateVar('element')
            ;
            return $this->handleView($view);
        /*}else {
            $view = $this->view('', 200)
                ->setTemplate("AppBundle:Campaign:classy_campaign_id.html.twig")
                ->setTemplateVar('element')
            ;
            return $this->handleView($view);;
        }*/

    }

    /**
     * This function use to return method name
     *
     * @param $className
     * @param array $methods
     * @return mixed
     */
    protected function getAttrMethod($className, $methods = array()){


        $mt = array_search('get'.str_replace('Values', '', $className), $methods, true);

        return $mt;
    }
}