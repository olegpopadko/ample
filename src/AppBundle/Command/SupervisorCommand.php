<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:10 PM
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use AppBundle\LogFile;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

/**
 * SupervisorCommand
 */
class SupervisorCommand extends EndlessContainerAwareCommand
{
    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setTimeout(5 * 60); //5 minutes
        $this->setTimeout(1); //5 minutes
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('supervisor:run')
            ->setDescription('Run import command for log files');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->files()->in('/home/*/logs')->name('*.log');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $filePath = $file->getRealPath();

            $user = $this->findOrCreateUserEntity($this->getUserNameFromPath($filePath));

            $this->runProcess($user, $filePath);
        }
    }

    /**
     * @param User $user
     * @param $filePath
     */
    private function runProcess(User $user, $filePath)
    {
        // Get memory leaks when using Symfony\Component\Process\Process
        shell_exec(sprintf('php %s/console --env=prod --no-debug import:run %s %s > /dev/null 2>&1 &',
            $this->getContainer()->getParameter('kernel.root_dir'), $user->getId(), $filePath));
    }

    /**
     * @return EntityManagerInterface
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $name
     * @return User|object
     */
    private function findOrCreateUserEntity($name)
    {
        $em   = $this->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy([
            'name' => $name
        ]);

        if (!$user) {
            $user = new User();
            $user->setName($name);
            $em->persist($user);
            $em->flush();
        }

        return $user;
    }

    /**
     * @param $path
     * @return mixed
     */
    private function getUserNameFromPath($path)
    {
        $folders = explode('/', $path);
        return $folders[2];
    }
}
