<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 3:00 PM
 */

namespace AppBundle\Form\Data;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\File;

/**
 * Class LineFilter
 * @package AppBundle\Form\Data
 */
class LineFilter
{
    /**
     * @var string
     */
    private $searchString;

    /**
     * @var bool
     */
    private $regex;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ArrayCollection|DateRange[]
     */
    private $datePeriods;

    /**
     *
     */
    public function __construct()
    {
        $this->datePeriods = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param $searchString
     * @return $this
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegex()
    {
        return $this->regex;
    }

    /**
     * @param $isRegex
     * @return $this
     */
    public function setRegex($isRegex)
    {
        $this->regex = $isRegex;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDatePeriods()
    {
        return $this->datePeriods;
    }

    /**
     * @param DateRange $datePeriod
     * @return $this
     */
    public function addDatePeriod($datePeriod)
    {
        $this->datePeriods->add($datePeriod);

        return $this;
    }

    /**
     * @param DateRange $datePeriod
     * @return $this
     */
    public function removeDatePeriod($datePeriod)
    {
        $this->datePeriods->removeElement($datePeriod);

        return $this;
    }
}
