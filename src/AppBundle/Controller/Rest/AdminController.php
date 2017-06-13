<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\Campaign;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use Ivory\CKEditorBundle\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BookingController
 *
 * @package AppBundle\Controller\Rest
 *
 * @RouteResource("Item")
 * @Rest\Prefix("/api")
 */
class AdminController extends FOSRestController
{

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Item",
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
    public function getItemAction($slug, $type){

        $em = $this->getDoctrine()->getManager();
        $cmp = $em->getRepository('AppBundle:'.$type)->find((int)$slug);
        $view = $this->view($cmp->getName(), 200)
            ->setTemplate("AppBundle:AdminMenu:breadcrumb.html.twig")
            ->setTemplateVar('cmp_label')
        ;

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  section="Item",
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
    public function getAttrShowAction(Request $request,$attrId,$status, $type)
    {
        try{
            if($status == 1){
                $status = 0;
            }else{
                $status = 1;
            }
            $em = $this->getDoctrine()->getManager();

            if($type == AttributesDefinition::IS_CAMPAIGN){
                $em->getRepository('AppBundle:AttributesCampaignSettings')->updateStatus($attrId, $status);
            }elseif ($type == AttributesDefinition::IS_PROJECT){
                $em->getRepository('AppBundle:AttributesProjectSettings')->updateStatus($attrId, $status);
            }elseif ($type == AttributesDefinition::IS_PAGE){
                $em->getRepository('AppBundle:AttributesPageSettings')->updateStatus($attrId, $status);
            }elseif ($type == AttributesDefinition::IS_LIST){
                $em->getRepository('AppBundle:AttributesListSettings')->updateStatus($attrId, $status);
            }

            return $status;
        }
        catch(Exception $e){
            echo $e->getMessage();die;
        }
    }


    /**
     * @ApiDoc(
     *  resource=true,
     *  section="Item",
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
    public function postPositionAttributeAction(Request $request)
    {

        $listObj = $request->get('object');

        try{
            $em = $this->getDoctrine()->getManager();
            foreach($listObj as $key => $val){
                $em->getRepository('AppBundle:ListValues')->updateModerator($val,$key);
            }
        }catch(Exception $e){
            echo $e->getMessage();die;
        }

        return true;
    }

    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Item",
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
    public function getMenuAction(){

        $em = $this->getDoctrine()->getManager();
        $campaigns = $em->getRepository('AppBundle:Campaign')->findOneBy(['state'=>Campaign::IS_ACTIVE]);

        $data = [];

        if(!$campaigns){
            $data['menuCampaigns']  = false ;
        }else{
            $data['menuCampaigns'] = true ;
        }

        $view = $this->view($data['menuCampaigns'], 200)
            ->setTemplate("AppBundle:Main:menu.html.twig")
            ->setTemplateVar('menu_data')
        ;

        return $this->handleView($view);
    }

}