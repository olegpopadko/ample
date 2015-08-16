<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @Route("/{fileId}", name="line")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($fileId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var LineRepository $repository */
        $repository = $em->getRepository('AppBundle:Line');

        $query = $repository->createQueryBuilder('l')
            ->where('l.file = :file')
            ->setParameter('file', $em->getReference('AppBundle:File', $fileId))
            ->orderBy('l.createdAt', 'desc')
            ->getQuery();

        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $this->get('app.per_page_service')->getPerPage()
        );

        return [
            'pagination'    => $pagination,
        ];
    }
}
