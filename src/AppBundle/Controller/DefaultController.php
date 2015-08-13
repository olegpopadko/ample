<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        $file      = "/home/vagrant/logs/NASA_access_log_Jul95.log";
//        $file = new \SplFileObject($file);
//        $file->seek(9);
//        echo $file->current();
//        exit;
        // replace this example code with whatever you need
        return $this->render('AppBundle:default:index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
