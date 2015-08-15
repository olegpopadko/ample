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
            $this->getPerPage($request->getSession())
        );

        return [
            'per_page_form' => $this->createPerPageForm($fileId, $request->getSession())->createView(),
            'pagination'    => $pagination,
        ];
    }

    /**
     * Set per page value.
     *
     * @Route("/{fileId}/update_lines_per_page", name="update_lines_per_page")
     * @Method("GET")
     * @Template()
     */
    public function updatePerPageAction($fileId, Request $request)
    {
        $form = $this->createPerPageForm($fileId, $request->getSession());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $request->getSession()->set($this->getPerPageKey(), $form->get('perPage')->getData());
        }

        return $this->redirect($this->generateUrl('line', ['fileId' => $fileId]));
    }

    /**
     * @param SessionInterface $session
     * @return mixed
     */
    private function getPerPage(SessionInterface $session)
    {
        return $session->get($this->getPerPageKey(), 10);
    }

    /**
     * @return string
     */
    private function getPerPageKey()
    {
        return 'file_lines_per_page';
    }

    /**
     * @param $fileId
     * @param SessionInterface $session
     * @return \Symfony\Component\Form\Form
     */
    private function createPerPageForm($fileId, SessionInterface $session)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('update_lines_per_page', ['fileId' => $fileId]))
            ->setMethod('GET')
            ->add('perPage', 'choice', [
                'choices' => [
                    10  => 10,
                    25  => 25,
                    50  => 50,
                    100 => 100,
                ],
            ])
            ->getForm();

        $form->get('perPage')->setData($this->getPerPage($session));

        return $form;
    }
}
