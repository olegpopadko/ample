<?php
/**
 * Created by Oleg Popadko
 * Date: 8/14/15
 * Time: 5:23 PM
 */

namespace AppBundle\LogFile;

/**
 * Class Line
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
        $this->line = trim($line);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->line);
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
     */
    public function getCreatedAt()
    {
        if ($this->isEmpty()) {
            return null;
        }
        try {
            $matches = [];
            preg_match('/\[(.*)\]/', $this->line, $matches);
            if (!empty($matches[1])) {
                return (new \DateTime($matches[1]))->setTimezone(new \DateTimeZone('UTC'));
            }
        } catch (\Exception $e) {

        }
    }
}
