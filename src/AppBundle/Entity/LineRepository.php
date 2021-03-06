<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LineRepository extends EntityRepository
{
    /**
     * @return Line
     */
    public function findLast(File $file)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.file = :file')
            ->setParameter('file', $file)
            ->orderBy('l.createdAt', 'desc')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

}
