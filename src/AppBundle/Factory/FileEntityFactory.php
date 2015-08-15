<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:44 PM
 */

namespace AppBundle\Factory;


use AppBundle\Entity\File;

/**
 * Class FileEntityFactory
 * @package AppBundle\Factory
 */
class FileEntityFactory
{
    /**
     * @param $filename
     * @return File
     */
    public function createFromFilename($filename)
    {
        $folders = explode('/', $filename);
        $name = $folders[count($folders) - 1];
        $entity = new File();
        $entity->setName($name);
        return $entity;
    }
}
