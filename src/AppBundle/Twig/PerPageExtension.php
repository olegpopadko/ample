<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 2:06 PM
 */

namespace AppBundle\Twig;

use AppBundle\Service\PerPage;

/**
 * Class PerPageExtension
 */
class PerPageExtension extends \Twig_Extension
{
    /**
     * @var PerPage
     */
    private $perPage;

    /**
     * @param PerPage $perPage
     */
    public function __construct(PerPage $perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('per_page_form', [$this, 'createPerPageFormView'], [
                'is_safe'           => ['html'],
                'needs_environment' => true
            ])
        ];
    }

    /**
     *
     */
    public function createPerPageFormView(\Twig_Environment $twig)
    {
        return $twig->render('AppBundle:PerPage:index.html.twig', [
            'per_page_form' => $this->perPage->createPerPageForm()->createView(),
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'per_page_extension';
    }
}
