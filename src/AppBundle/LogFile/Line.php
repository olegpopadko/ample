<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:23 PM
 */

namespace AppBundle\LogFile;

/**
 * Class Line
 * @package AppBundle\LogFile
 */
class Line
{
    /**
     * @var string
     */
    private $line;

    /**
     * @param $line
     */
    public function __construct($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->line;
    }

    /**
     * @return \DateTime
     * @throws InvalidLineFormatException
     */
    public function getCreatedAt()
    {
        $matches = [];
        preg_match('/\[(.*)\]/', $this->line, $matches);
        if (empty($matches[1])) {
            throw new InvalidLineFormatException();
        }
        return new \DateTime($matches[1]);
    }
}
