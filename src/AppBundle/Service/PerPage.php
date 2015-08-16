§<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 1:35 PM
 */

namespace AppBundle\Service;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class PerPage
 */
class PerPage
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var null
     */
    private $value = null;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session, FormFactory $formFactory)
    {
        $this->session = $session;
        $this->formFactory = $formFactory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $form = $this->createPerPageForm();
        $form->handleRequest($event->getRequest());
        if ($form->isValid()) {
            $this->value = $form->get($this->getFormFieldName())->getData();
        }
    }

    /**
     * @return string
     */
    private function getFormName()
    {
        return 'per_page_form';
    }

    /**
     * @return Form
     */
    public function createPerPageForm()
    {
        $form = $this->formFactory->createNamedBuilder($this->getFormName(), 'form', [
            'perPage' => $this->getPerPage(),
        ])
            ->setMethod('GET')
            ->add($this->getFormFieldName(), 'choice', [
                'choices' => $this->getPerPageValues(),
            ])
            ->getForm();

        return $form;
    }

    /**
     * @return string
     */
    private function getFormFieldName()
    {
        return 'perPage';
    }

    /**
     * @return array
     */
    private function getPerPageValues()
    {
        return [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
        ];
    }

    /**
     * @return mixed
     */
    private function getDefaultValue()
    {
        $values = $this->getPerPageValues();
        return array_shift($values);
    }

    /**
     * @param SessionInterface $session
     * @return mixed
     */
    public function getPerPage()
    {
        if (!is_null($this->value)) {
            $this->session->set($this->getPerPageKey(), $this->value);
            $this->value = null;
        }
        return $this->session->get($this->getPerPageKey(), $this->getDefaultValue());
    }

    /**
     * @return string
     */
    private function getPerPageKey()
    {
        return 'file_lines_per_page';
    }
}
