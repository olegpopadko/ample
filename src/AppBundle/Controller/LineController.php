<?php

namespace AppBundle\Controller;


use AppBundle\Entity\LineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Line;

/**
 * Line controller.
 *
 * @Route("/line")
 */
class LineController extends Controller
{

    /**
     * Lists all Line entities.
     *
     * @Route("/", name="line")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var LineRepository $repository */
        $repository = $em->getRepository('AppBundle:Line');

        $entities = $repository->createQueryBuilder('l')
            ->select()
            ->getQuery()
            ->setMaxResults(100)
            ->execute();

        return array(
            'entities' => $entities,
        );
    }
}
