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
use Symfony\Component\Filesystem\LockHandler;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\File;
use AppBundle\Entity\Line;
use AppBundle\Entity\LineRepository;
use AppBundle\Entity\User;
use AppBundle\Factory\FileEntityFactory;
use AppBundle\Factory\LineEntityFactory;
use AppBundle\LogFile;
use Wrep\Daemonizable\Command\EndlessContainerAwareCommand;

/**
 * ImportCommand
 *
 * @package    AppBundle
 * @subpackage Command
 */
class ImportCommand extends EndlessContainerAwareCommand
{
    /**
     * @var int
     */
    private $linePosition = 0;

    /**
     * @var int
     */
    private $fileId;

    /**
     * @var int
     */
    private $lastLineId;

    /**
     * @var LockHandler
     */
    private $lockHandler;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('import:run')
            ->setDescription('Import log file to DB')
            ->addArgument('user_id', InputArgument::REQUIRED, 'User Id')
            ->addArgument('filename', InputArgument::REQUIRED, 'Path to file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function starting(InputInterface $input, OutputInterface $output)
    {
        parent::starting($input, $output);

        $this->lockHandler = new LockHandler($input->getArgument('filename') . '.lock');
        if (!$this->lockHandler->lock()) {
            $this->shutdown();
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        clearstatcache();
        $file = new \SplFileObject($filename);

        /** @var LineEntityFactory $lineEntityFactory */
        $lineEntityFactory = $this->getContainer()->get('app.line_entity_factory');

        $em = $this->getManager();

        $fileEntity = $this->findOrCreateFileEntity($filename, $input->getArgument('user_id'));

        $this->setFileId($fileEntity->getId());

        $this->initLastLineId();

        $i         = 0;
        $batchSize = 10000;

        $file->seek($this->linePosition);
        while (!$file->eof()) {
            $line = new LogFile\Line($file->fgets());
            if (!$this->isLineAccepted($line)) {
                continue;
            }
            $em->persist($lineEntityFactory->createFromLine($line, $this->getFileEntity()));
            $i++;
            if ($i % $batchSize === 0) {
                $em->flush();
                $em->clear();
            }
        }
        $this->linePosition = $file->key();
        $em->flush();
        $em->clear();
    }

    /**
     * @param LogFile\Line $line
     * @return bool
     */
    private function isLineAccepted(LogFile\Line $line)
    {
        $lastLineEntity = $this->getLastLineEntity();

        if ($line->isEmpty()) {
            return false;
        }

        $createdAtSmaller = $lastLineEntity && $line->getCreatedAt() < $lastLineEntity->getCreatedAt();
        $sameLines        = $lastLineEntity
            && $line->getCreatedAt() == $lastLineEntity->getCreatedAt()
            && $line->getContent() === $lastLineEntity->getContent();

        if ($createdAtSmaller || $sameLines) {
            return false;
        }

        return true;
    }

    /**
     * @param $fileId
     */
    private function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return File
     */
    private function getFileEntity()
    {
        if ($this->fileId) {
            return $this->getManager()->getReference('AppBundle:File', $this->fileId);
        }
    }

    /**
     * @return EntityManagerInterface
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @param $filename
     * @param $userId
     * @return \AppBundle\Entity\File|object
     */
    private function findOrCreateFileEntity($filename, $userId)
    {
        $em = $this->getManager();

        /** @var User $user */
        $user = $em->getReference('AppBundle:User', $userId);

        /** @var FileEntityFactory $fileEntityFactory */
        $fileEntityFactory = $this->getContainer()->get('app.file_entity_factory');

        $fileEntity = $fileEntityFactory->findOrCreate($filename, $user);

        $em->persist($fileEntity);
        $em->flush();

        return $fileEntity;
    }

    /**
     * @return int
     */
    private function initLastLineId()
    {
        /** @var LineRepository $repository */
        $repository = $this->getManager()->getRepository('AppBundle:Line');

        if ($line = $repository->findLast($this->getFileEntity())) {
            $this->lastLineId = $line->getId();
        }
    }

    /**
     * @return Line
     */
    private function getLastLineEntity()
    {
        if ($this->lastLineId) {
            return $this->getManager()->getReference('AppBundle:Line', $this->lastLineId);
        }
    }
}
