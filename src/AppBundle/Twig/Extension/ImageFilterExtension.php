<?php
/**
 * Created by PhpStorm.
 * User: aram
 * Date: 12/11/15
 * Time: 4:26 PM
 */


namespace AppBundle\Twig\Extension;

use Imagine\Gd\Image;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ImageFilterExtension
 * @package AppBundle\Twig\Extension
 */
class ImageFilterExtension extends \Twig_Extension
{
    /**
     * @var
     */
    private $container;

    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('lbImageFilter', array($this, 'lbImageFilter')),
        );
    }


    /**
     * @param $path
     * @param $filter
     */
    public function lbImageFilter($path, $filter)
    {

        /*if(strpos($path, '.svg') !== false){
            $dir = str_replace('/app', '/web', $this->container->getParameter('kernel.root_dir'));
            $path = $this->convertTopng($dir , $path);
        }*/
        // check has http in path
        if(strpos($path, 'http') === false){

            try{
                $this->container->get('liip_imagine.controller')->filterAction($this->container->get('request'), $path, $filter);
                $cacheManager = $this->container->get('liip_imagine.cache.manager');
                $srcPath = $cacheManager->getBrowserPath($path, $filter);

                return $srcPath;
            }catch (\Exception $e){
                return $path;
            }
        }
        else{
            return $path;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_lb_image_filter';
    }

    public function convertToPng($dir, $path){

        $t = file_get_contents($dir.$path);
        $type = pathinfo($path);
        $img = new \Imagick();
        $img->readImageBlob($t);
        $img->paintTransparentImage($img->getImageBackgroundColor(), 0, 10000);
        $img->setImageFormat("png32");

        $fl = $dir.$type['dirname'].'/' . $type['filename'] . '.png';
        $fs = new Filesystem();

        $fs->touch($fl, time(), time() - 10);

        $fs->chown($fl, 'www-data', true);

        file_put_contents($fl, $img, FILE_APPEND);
        return str_replace('.svg', '.png', $path);

        }
}