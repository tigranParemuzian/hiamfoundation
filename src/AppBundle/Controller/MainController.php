<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AttributesDefinition;
use AppBundle\Entity\Campaign;
use AppBundle\Entity\CollectionValues;
use AppBundle\Entity\DateValues;
use AppBundle\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use AppBundle\Entity\Image;
use AppBundle\Entity\ListValues;
use AppBundle\Entity\Project;
use AppBundle\Entity\TextValues;
use AppBundle\Entity\BooleanValues;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{
    private function getData($slug){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('AppBundle:Page')->findForShow($slug);
        $data=[];
        foreach ($page->getSettings() as $setting){
            $attr = $setting->getAttributesDefinition();
            $data[$attr->getAttrName()] = $em->getRepository('AppBundle:'.$attr->getAttrClass())->findForViuew(AttributesDefinition::IS_PAGE, $page->getId(), $attr->getAttrName());
        }
        return ['name'=>'ONEArmenia | Together we will build One, thriving Armenia.', 'page'=>$page, 'data'=>$data, "instagram"=>$this->getInstagramLast(),  "blog"=>$this->getBlogLast(), "projects" => $this->getProjects(), "rand_project" =>$this->getRandProject(), "interviewer" => $this->getRandOneonone()];
    }

    private function getRandOneonone(){
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('AppBundle:Page')->findForShow('one-on-one');

        $interviewers  = $em->getRepository('AppBundle:CollectionValues')->findInterviewer($page->getId(), AttributesDefinition::IS_PAGE );
        foreach ($interviewers as $interviewer){
            if($interviewer instanceof CollectionValues){
                $inter = $interviewer;
            }
        }
        $interviewer = $inter->getListValues()[rand(0, count($inter->getListValues())-1)];

        return $interviewer;
    }

    private function getInstagramLast(){
        $url = "https://api.instagram.com/v1/users/205913653/media/recent/?access_token=205913653.1677ed0.3b1ab3ec362947f786afb974adb74a54&count=1";
        $result = file_get_contents($url);
        $result = json_decode($result, true);
        $last_photo["image"] =  $result['data'][0]['images']['standard_resolution']["url"];
        $last_photo["link"] =  $result['data'][0]['link'];
        $last_photo["text"] = $result['data'][0]['caption']['text'];
        if(strlen($result['data'][0]['caption']['text']) > 50) {
            $last_photo["text"] = substr($result['data'][0]['caption']['text'], 0, 45) . '...';
        }
        return $last_photo;
    }

    private  function getBlogLast(){
        $url = "https://blog.onearmenia.org/latest?format=json";
        $result = file_get_contents($url);
        $result = str_replace("])}while(1);</x>","", $result);
        $result = json_decode($result, true);
        $last_photo["image"] =  "https://cdn-images-1.medium.com/max/400/".$result['payload']['posts'][0]['virtuals']['previewImage']['imageId'];
        $last_photo["link"] = "https://blog.onearmenia.org/". $result['payload']['posts'][0]['id'];
        $last_photo["text"] = $result['payload']['posts'][0]['title'];
        if(strlen($result['payload']['posts'][0]['title']) > 55) {
            $last_photo["text"] = substr($result['payload']['posts'][0]['title'], 0, 50) . '...';
        }
        return $last_photo;
    }

    public function getProjects(){
        $em = $this->getDoctrine()->getManager();
        $infos = $em->getRepository('AppBundle:Project')->findCategories();
        $data = [];
        foreach ($infos as $info){
            if($info instanceof Project){
                $data[$info->getId()] = $info;
            }
            if($info instanceof ListValues){
                $data[$info->getBelongsToObject()]->addValue($info);
            }
        }
        return $data;
    }

    function getRandProject(){
        $projects  = $this->getProjects();
        array_shift($projects);
        $rand = array_rand($projects);
        return  $projects[$rand];
    }


    /**
     * @Route("/remove-image/{filename}/{object}", name="remove_image")
     * @Security("has_role('ROLE_USER')")
     * @param $filename
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function removeImageAction($filename, $object)
    {
        try{
            // get entity manager
            $em = $this->getDoctrine()->getManager();

            // get object by className
            $object = $em->getRepository($object)->findOneBy(array('fileName' => $filename));

            // get origin file path
            $filePath = $object->getAbsolutePath() . $object->getFileName();

            // get doctrine
            $em = $this->getDoctrine()->getManager();

            // check file and remove
            if (file_exists($filePath) && is_file($filePath)){
                unlink($filePath);
            }

            $object->setFileName(null);
            $object->setFileOriginalName(null);

            $em->persist($object);
            $em->flush();

            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        catch(\Exception $e){
            throw $e;
        }

    }

    /**
     * @Route("/progressbar/{objectId}/{object}", name="progressbar_image")
     * @Security("has_role('ROLE_USER')")
     * @param $objectId
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function progressbarAction($objectId, $object)
    {
        try{
            // get entity manager
            $em = $this->getDoctrine()->getManager();

            $objectValues = $em->getRepository('AppBundle:'.$object)->findDataById($objectId);

            $objectValues = $em->getRepository('AppBundle:ListValues')->findProgress($object, $objectId);

            $values = [];
            $methods = [];
            $count = 0;
            $var = 0;

            foreach ($objectValues as $objectValue){

                if($objectValue instanceof ListValues){
                    $values[$objectValue->getId()] = $objectValue;
                    $methods = get_class_methods($objectValue);
                }

                if($objectValue instanceof AttributesDefinition){

                    $objectValue->getId();
                    $methodKay = $this->getAttrMethod($objectValue->getAttrClass(), $methods);

                    $prams = $values[$objectValue->getBelongsToObject()]->$methods[$methodKay]();

                    foreach ($prams as $pram) {

                        if($pram instanceof File || $pram instanceof Image) {
                            if($pram->getFileOriginalName()){
                                $var ++;
                            }
                        }

                        if($pram instanceof TextValues || $pram instanceof DateValues) {

                            if($pram->getValue()){
                                $var ++;
                            }
                        }

                        $count ++;

                    }
                    $values[$objectValue->getBelongsToObject()]->addAttributesDefinition($objectValue);
                }
            }
            $var>0 ? $persent = round($var/$count * 100, 2):$persent = 0;

            return ['persent'=>$persent];

        }
        catch(\Exception $e){
            throw $e;
        }

    }

    /**
     * @Route("/attr-list/{objectId}/{object}", name="attr_list")
     * @Security("has_role('ROLE_USER')")
     * @param $objectId
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function attrListAction($objectId, $object)
    {
        try{
            // get entity manager
            $em = $this->getDoctrine()->getManager();

            if($object === AttributesDefinition::IS_COLLECTION){
                $objectValues = $em->getRepository('AppBundle:'.$object)->findDataById($objectId);

            }else {

                $objectValues = $em->getRepository('AppBundle:'.$object)->findDataById($objectId);
            }

            foreach($objectValues as $key => $val){
                $proj_url = $val->getSlug();
                break;
            }
            return ['proj_url'=>$proj_url, 'objectValues'=>$objectValues, 'objectType'=>$object];

        }
        catch(\Exception $e){
            throw $e;
        }

    }

    /**
     * @Route("/show-file/{id}/{name}", name="show_file")
     * @param $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function showPdfAction($id, $name){

        $em = $this->getDoctrine()->getManager();

        $file = $em->getRepository('AppBundle:File')->find((int)$id);

        if(!$file){

            return $this->redirectToRoute('page-not-found');
        }
        $fs = new Filesystem();
        $dir = str_replace('/app', '/web', $this->container->getParameter('kernel.root_dir'));
        $fileName = str_replace(" ", "_", urldecode($name) );
        try {

            if($fs->exists($dir."/uploads/files/{$fileName}") && $file->getFileName() != "{$fileName}"){
                unlink($dir."/uploads/files/{$fileName}");
            }

            $fs->copy($dir.$file->getDownloadLink(), $dir .'/uploads/files/'.$fileName, true);
            $fs->chown($dir .'/uploads/files/'.$fileName, 'www-data', true);
        } catch(\Exception $e){
            throw $e;
        }

        $path_parts = pathinfo($dir .'/uploads/files/'.$fileName);

        if($path_parts['extension'] == 'pdf'){
            return $this->render('@App/Pages/show_pdf.html.twig', array('file'=>'/uploads/files/'.$fileName, 'file_name'=>$fileName));
        }else {
            $response = new Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($dir .'/uploads/files/'.$fileName));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($dir .'/uploads/files/'.$fileName) . '";');
            $response->headers->set('Content-length', filesize($dir .'/uploads/files/'.$fileName));

            $response->sendHeaders();

            $response->setContent(file_get_contents($dir .'/uploads/files/'.$fileName));

            return $response;
        }
    }


    /**
     * @Route("/document-find/{objName}/{objClass}/{blObjClass}/{blObjId}", name="documents_find")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param $objectId
     * @param $objClass
     * @param $blObjClass
     * @param $blObjId
     */
    public function documentFindAction(Request $request, $objName, $objClass, $blObjClass, $blObjId){

        $em = $this->getDoctrine()->getManager();

        $onjId = $em->getRepository('AppBundle:'.$objClass)->findUniq($objName, $blObjId, $blObjClass);

        if($objClass == 'CollectionValues'){

            return $this->redirectToRoute('admin_app_collectionvalues_edit', ['id'=>$onjId->getId()]);
        }else {
            return $this->redirectToRoute('admin_app_listvalues_edit', ['id'=>$onjId->getId()]);
        }

    }

    /**
     * @Route("/attr-list-collection/{objectId}/{object}", name="attr_list_collection")
     * @Security("has_role('ROLE_USER')")
     * @param $objectId
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function attrListCollectionAction($objectId, $object)
    {
        try{
            // get entity manager
            $em = $this->getDoctrine()->getManager();

            if($object === AttributesDefinition::IS_COLLECTION){
                $objectValues = $em->getRepository('AppBundle:'.$object)->findDataById($objectId);
            }else {

                $objectValues = $em->getRepository('AppBundle:'.$object)->findProgress($object, $objectId);
            }
            $object = [];
            $values = [];
            $isIsset = [];
            $methods = [];

            foreach ($objectValues as $objectValue){
                if($objectValue instanceof CollectionValues){
                    $object = $objectValue;
                    foreach ($objectValue->getListValues()->getValues() as $val){
                        $values[$val->getId()] = $val;
                    }
//                    $values = $objectValue->getListValues()->getValues();
                    $methods = get_class_methods($objectValue->getListValues()->first());
                }

                /*if($objectValue instanceof ListValues){
                    $values[$objectValue->getId()] = $objectValue;
                    $methods = get_class_methods($objectValue);
                }*/

                if($objectValue instanceof AttributesDefinition){

                    $methodKay = $this->getAttrMethod($objectValue->getAttrClass(), $methods);

                    $prams = $values[$objectValue->getBelongsToObject()]->$methods[$methodKay]();

                    foreach ($prams as $pram) {

                        if($pram instanceof File || $pram instanceof Image) {
                            if($pram->getFileOriginalName()){
                                $isIsset[$objectValue->getBelongsToObject()][] = 1;
                            }else{
                                $isIsset[$objectValue->getBelongsToObject()][] = 0;
                            }
                        }

                        if($pram instanceof TextValues || $pram instanceof DateValues) {

                            if($pram->getValue()){
                                $isIsset[$objectValue->getBelongsToObject()][] = 1;
                            }else {
                                $isIsset[$objectValue->getBelongsToObject()][] = 0;
                            }
                        }
                    }
                    $values[$objectValue->getBelongsToObject()]->addAttributesDefinition($objectValue);
                }
            }

            foreach ($isIsset as $kay=>$item){
                array_search(0,$item)? $isIsset[$kay] = 0 : $isIsset[$kay] = 1;

            }

            return ['settings'=>$values, 'isIsset'=>$isIsset];

        }
        catch(\Exception $e){
            throw $e;
        }

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


    /**
     * @Route("/attr-show/{attrId}/{status}", name="attr_show")
     * @Security("has_role('ROLE_USER')")
     * @param $objectId
     * @param $object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Template()
     */
    public function attrShoeAction($attrId, $status)
    {
        try{
            // get entity manager
            $em = $this->getDoctrine()->getManager();

            $object = $em->getRepository('AppBundle:AttributesDefinition')->updateStatus($attrId, $status);

            return $this->redirect($_SERVER['HTTP_REFERER']);

        }
        catch(\Exception $e){
            throw $e;
        }

    }

    /**
     * @Route("/page-not-found", name="page-not-found")
     * @Template()
     */
    public function notFoundAction()
    {
        return $this->render('AppBundle:Pages:404.html.twig');
    }

    private function cacheableData($kay, $state, $data = null){
        if(extension_loaded('apc') && ini_get('apc.enabled')) {

            if($state === true && !is_null($data)){
                apc_store($kay, $data, 86400);
            }else{

                $data = apc_fetch($kay);
            }
        }

        return $data;
    }
}
