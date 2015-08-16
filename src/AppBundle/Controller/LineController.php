<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LineRepository;
use AppBundle\Form\Data\LineFilter;
use AppBundle\Form\LineType;
use Doctrine\ORM\QueryBuilder;
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
        list($queryBuilder, $lineFilter) = $this->createQueryBuilder($request);

        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $this->get('app.per_page_service')->getPerPage()
        );

        return [
            'filter_form' => $this->createFilterForm($lineFilter)->createView(),
            'pagination'  => $pagination,
        ];
    }

    /**
     * @param $request
     * @return array
     */
    private function createQueryBuilder($request)
    {
        $lineFilter = $this->getLineFilter($request);

        $tableAlias = 'l';

        $queryBuilder = $this->getLineRepository()->createQueryBuilder($tableAlias)
            ->orderBy('l.createdAt', 'desc');

        $this->get('app.apply_line_filter_factory')
            ->create($queryBuilder, $tableAlias, $lineFilter)
            ->modify();

        return [$queryBuilder, $lineFilter];
    }

    /**
     * @return LineRepository
     */
    private function getLineRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBundle:Line');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     * @return LineFilter
     */
    private function getLineFilter(Request $request)
    {
        $lineFilter = new LineFilter();

        $form = $this->createFilterForm($lineFilter);
        $form->handleRequest($request);
        if ($form->isValid()) {
            return $lineFilter;
        } else {
            return new LineFilter();
        }

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
