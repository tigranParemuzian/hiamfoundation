<?php
/**
 * Created by PhpStorm.
 * User: tigran
 * Date: 3/16/17
 * Time: 6:57 PM
 */

namespace AppBundle\Service;


use Symfony\Component\DependencyInjection\Container;

class ClassyApi
{
    const CLASSY_ID = 118524;
    const ClASSY_ACCOUNT_ID = 'P8ob8ye56zkVT5Sl';
    const CLASSY_SECRET = 'zztREVhQQoBRg7hY';


    /**
     * Symfony\Component\DependencyInjection\Container
     *
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /*This action return valid token for each request to classy account*/
    private function  classyToken(){

        $ch = curl_init();
        $data = [
            'grant_type'=>'client_credentials',
            'client_id'=> self::ClASSY_ACCOUNT_ID,
            'client_secret'=> self::CLASSY_SECRET
        ];

        curl_setopt($ch, CURLOPT_URL,"https://api.classy.org/oauth2/auth");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_CAINFO,$_SERVER['DOCUMENT_ROOT'].'/cacert.pem');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

        $server_output = curl_exec ($ch);

        try{
            curl_close ($ch);
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        return  $server_output;
    }


    /*Get  donation list with pagination attributes*/
    public function getDonation($count,$id, $page){


        //$classy = $this->findClassyId();
        //$id = self::CLASSY_ID;
        $token =  json_decode($this->classyToken());
        //$classy_id = $id;

        $ch = curl_init();
        $url = "https://api.classy.org/2.0/campaigns/$id/transactions?per_page=$count&page=$page&sort=purchased_at:desc&filter=status=success";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CAINFO,$_SERVER['DOCUMENT_ROOT'].'/cacert.pem');
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: Bearer $token->access_token"));

        $server_output = curl_exec ($ch);
        try{
            curl_close ($ch);
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        return json_decode($server_output,true);
    }

    /*return fundraising list with pagination attribute*/
    public function getFundraising($count,$id, $page){

        //$id = self::CLASSY_ID;
        /*if(strpos($_SERVER['HTTP_REFERER'],'santas_wanted_2016')){
            $id=105746;
        }
        if(strpos($_SERVER['HTTP_REFERER'],'hye_tech_kids')){
            $id=82068;
        }
        if(strpos($_SERVER['HTTP_REFERER'],'hike_armenia')){
            $id=58801;
        }
        if(strpos($_SERVER['HTTP_REFERER'],'bring_on_the_buzz')){
            $id=69161;
        }*/

        $token =  json_decode($this->classyToken());

        $ch = curl_init();
        $url = "https://api.classy.org/2.0/campaigns/$id/fundraising-pages?aggregates=true&sort=total_raised:desc&per_page=$count&page=$page&with=member&filter=status=active";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_CAINFO,$_SERVER['DOCUMENT_ROOT'].'/cacert.pem');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: Bearer $token->access_token"));

        $server_output = curl_exec ($ch);
        try{
            curl_close ($ch);
        }catch(\Exception $e){
            echo $e->getMessage();
        }

        return  json_decode($server_output, true);

    }

    public function getInfoAction($id){

        $token =  json_decode($this->classyToken());
        $classy_id = self::CLASSY_ID;
        $classy_id = $id;

        $ch = curl_init();
        $url = "https://api.classy.org/2.0/campaigns/$classy_id";
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch,CURLOPT_CAINFO,$_SERVER['DOCUMENT_ROOT'].'/cacert.pem');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array("Authorization: Bearer $token->access_token"));

        $server_output = curl_exec ($ch);
        try{
            curl_close ($ch);
        }catch(\Exception $e){
            echo $e->getMessage();
        }

        return  $server_output;

    }


    /**
     * @param $objectId
     * @param $type
     */
    public function findClassyId($type, $objectId = null){

        $classy_id = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:ListValues')->findClassyId($type, $objectId );

        if(!$classy_id){
            return null;
        }

        if(!$classy_id->getText()->isEmpty()) {

            $classy_id = $classy_id->getText()->first()->getValue();

        }

        return $classy_id;

    }

}