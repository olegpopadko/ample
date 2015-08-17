<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:44 PM
 */

namespace AppBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\File;
use AppBundle\Entity\User;

/**
 * Class FileEntityFactory
 */
class FileEntityFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $filename
     * @return File
     */
    public function create($filename, User $user)
    {
        $entity  = new File();
        $entity->setName($this->getName($filename));
        $entity->setUser($user);
        return $entity;
    }

    /**
     * @param $filename
     * @param User $user
     * @return File|object
     */
    public function findOrCreate($filename, User $user)
    {
        $file = $this->em->getRepository('AppBundle:File')->findOneBy([
            'name' => $this->getName($filename),
            'user' => $user,
        ]);

        if (!$file) {
            $file = $this->create($filename, $user);
        }

        return $file;
    }

    private function getName($filename)
    {
        $folders = explode('/', $filename);
        return $folders[count($folders) - 1];
    }
}
