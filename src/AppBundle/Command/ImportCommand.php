<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:10 PM
 */

namespace AppBundle\Command;

use AppBundle\Entity\File;
use AppBundle\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\LogFile;

/**
 * ImportCommand
 *
 * @package    AppBundle
 * @subpackage Command
 */
class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('file:import')
            ->setDescription('Import log file to DB')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to file');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = new \SplFileObject($input->getArgument('filename'));

        $fileEntityFactory = $this->getContainer()->get('app_bundle.file_entity_factory');
        $lineEntityFactory = $this->getContainer()->get('app_bundle.line_entity_factory');

        $fileEntity = $fileEntityFactory->createFromFilename($file);

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $em->persist($fileEntity);
        $em->flush();

        $i = 0;
        $batchSize = 10000;

        foreach ($file as $line) {
            $em->persist($lineEntityFactory->createFromLine(new LogFile\Line($line), $fileEntity));
            $i++;
            if ($i % $batchSize === 0) {
                $em->flush();
            }
        }
        $em->flush();
    }
}
