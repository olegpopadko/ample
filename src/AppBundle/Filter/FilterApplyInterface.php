<?php
/**
 * Created by Oleg Popadko
 * Date: 8/16/15
 * Time: 6:32 PM
 */

namespace AppBundle\Filter;

/**
 * Interface FilterApplyInterface
 */
interface FilterApplyInterface
{
    /**
     * @return mixed
     */
    public function modify();
}
