<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 6:29 PM
 */

namespace AppBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use AppBundle\Form\Data\LineFilter;

/**
 * Class ApplyLineFilterFactory
 */
class ApplyLineFilterFactory
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param $tableAlias
     * @param LineFilter $lineFilter
     * @return FilterApplyInterface
     */
    public function create(QueryBuilder $queryBuilder, $tableAlias, LineFilter $lineFilter)
    {
        return new ApplyLineFilter($queryBuilder, $tableAlias, $lineFilter);
    }
}
