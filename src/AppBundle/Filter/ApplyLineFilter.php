<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 6:29 PM
 */

namespace AppBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\Data\DateRange;
use AppBundle\Form\Data\LineFilter;

/**
 * Class ApplyLineFilter
 */
class ApplyLineFilter implements FilterApplyInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    /**
     * @var
     */
    private $tableAlias;
    /**
     * @var LineFilter
     */
    private $lineFilter;

    /**
     * @param QueryBuilder $queryBuilder
     * @param $tableAlias
     * @param LineFilter $lineFilter
     */
    public function __construct(QueryBuilder $queryBuilder, $tableAlias, LineFilter $lineFilter)
    {
        $this->queryBuilder = $queryBuilder;
        $this->tableAlias   = $tableAlias;
        $this->lineFilter   = $lineFilter;
    }

    /**
     *
     */
    public function modify()
    {
        $this->addSearchStringFilter();

        $this->addFileFilter();

        $this->addDataRangeFilter();
    }

    /**
     *
     */
    private function addSearchStringFilter()
    {
        if ($searchString = $this->lineFilter->getSearchString()) {
            $searchStringKey = 'string';
            if ($this->lineFilter->isRegex()) {
                $this->queryBuilder->andWhere($this->getRegexpWhere($searchStringKey));
            } else {
                $this->queryBuilder->andWhere($this->getLikeWhere($searchStringKey));
                $searchString = '%' . $searchString . '%';
            }
            $this->queryBuilder->setParameter($searchStringKey, $searchString);
        }
    }

    /**
     *
     */
    private function addFileFilter()
    {
        if ($file = $this->lineFilter->getFile()) {
            $key = 'file';
            $this->queryBuilder->andWhere($this->getEqWhere($key));
            $this->queryBuilder->setParameter($key, $file);
        }
    }

    /**
     * @param $key
     * @return string
     */
    private function getEqWhere($key)
    {
        return sprintf('%s = :%s', $this->getField('file'), $key);
    }

    /**
     * @param string $key
     * @return string
     */
    private function getRegexpWhere($key)
    {
        return sprintf('REGEXP(%s, :%s) = 1', $this->getField('content'), $key);
    }

    /**
     * @return string
     */
    private function getLikeWhere($key)
    {
        return sprintf('%s like :%s', $this->getField('content'), $key);
    }

    /**
     * @param $name
     * @return string
     */
    private function getField($name)
    {
        return $this->tableAlias . '.' . $name;
    }

    /**
     *
     */
    private function addDataRangeFilter()
    {
        if (!$this->lineFilter->getDatePeriods()->isEmpty()) {
            $wheres = [];
            /** @var DateRange $dateRange */
            foreach ($this->lineFilter->getDatePeriods() as $key => $dateRange) {
                $startDateKey = 'startDate' . $key;
                $endDateKey   = 'endDate' . $key;
                $this->queryBuilder->setParameter($startDateKey, $dateRange->getStartDate());
                $this->queryBuilder->setParameter($endDateKey, $dateRange->getEndDate());
                $wheres[] = $this->queryBuilder->expr()
                    ->andX($this->getBetweenWhere($startDateKey, $endDateKey));
            };

            $this->queryBuilder->andWhere(call_user_func_array([$this->queryBuilder->expr(), 'orX'], $wheres));
        }
    }

    /**
     * @param $key1
     * @param $key2
     * @return string
     */
    private function getBetweenWhere($key1, $key2)
    {
        return sprintf('%s between :%s and :%s', $this->getField('createdAt'), $key1, $key2);
    }
}
