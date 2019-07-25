<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuartetController
 * @package App\Controller
 *
 * @Route("/quartet")
 */
class QuartetController extends Controller
{

    /**
     * @Route(path="/", name="quartet-homepage")
     * @param Request $request
     *
     */
    public function homepageAction(Request $request){

        return $this->render(':quartet:homepage.html.twig');
    }


    /**
     * @Route("/discography", name="discography")
     * @param Request $request
     *
     */
    public function discographyAction(Request $request){

        return $this->render(':quartet:discography.html.twig');
    }

    /**
     * @Route("/member-bio", name="member-bio")
     * @param Request $request
     *
     */
    public function memberBioAction(Request $request){

        return $this->render(':quartet:member_bio.html.twig');
    }

    /**
     * @Route("/performance-calendar", name="performance-calendar")
     * @param Request $request
     *
     */
    public function performanceCalendarBioAction(Request $request){

        return $this->render(':quartet:performance_calendar.html.twig');
    }

    /**
     * @Route("/videos", name="videos")
     * @param Request $request
     *
     */
    public function videosAction(Request $request){

        return $this->render(':quartet:videos.html.twig');
    }

    /**
     * @Route("/quartet-news", name="quartet-news")
     * @param Request $request
     *
     */
    public function quartetNewsAction(Request $request){

        return $this->render(':quartet:quartet_news.html.twig');
    }

    /**
     * @Route("/contact-us", name="contact-us")
     * @param Request $request
     *
     */
    public function contactUsAction(Request $request){

        return $this->render(':quartet:contact_us.html.twig');
    }

}