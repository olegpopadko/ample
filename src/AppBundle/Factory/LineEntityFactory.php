<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:44 PM
 */

namespace AppBundle\Factory;

use AppBundle\Entity\File;
use AppBundle\Entity\Line;
use AppBundle\LogFile;

/**
 * Class LineEntityFactory
 * @package AppBundle\Factory
 */
class LineEntityFactory
{
    /**
     * @param LogFile\Line $line
     * @param File $file
     * @return Line
     * @throws LogFile\InvalidLineFormatException
     */
    public function createFromLine(LogFile\Line $line, File $file)
    {
        $entity = new Line();
        $entity->setCreatedAt($line->getCreatedAt());
        $entity->setContent($line->getContent());
        $entity->setFile($file);
        return $entity;
    }
}
