<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LineRepository;
use AppBundle\Form\Data\DateRange;
use AppBundle\Form\Data\LineFilter;
use AppBundle\Form\LineType;
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
     * @Route("/", name="line")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var LineRepository $repository */
        $repository = $em->getRepository('AppBundle:Line');

        $query = $repository->createQueryBuilder('l')
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
            'filter_form' => $this->createFilterForm($this->getLineFilter())->createView(),
            'pagination'  => $pagination,
        ];
    }

    /**
     * @return $this
     */
    private function getLineFilter()
    {
        $dateRange = new DateRange();
        $dateRange->setStartDate((new \DateTime())->modify('-10 days'))
            ->setEndDate(new \DateTime());
        return (new LineFilter())->addDatePeriod($dateRange);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createFilterForm($lineFilter)
    {
        return $this->createForm(new LineType(), $lineFilter, [
            'method' => 'GET',
        ])
            ->add('submit', 'submit');
    }
}
