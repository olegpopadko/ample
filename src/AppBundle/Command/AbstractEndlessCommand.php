<?php
/**
 * Created by Oleg Popadko
 * Date: 8/17/15
 * Time: 6:59 AM
 */

namespace AppBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Wrep\Daemonizable\Command\EndlessCommand;

abstract class AbstractEndlessCommand extends EndlessCommand
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @return ContainerInterface
     *
     * @throws \LogicException
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
