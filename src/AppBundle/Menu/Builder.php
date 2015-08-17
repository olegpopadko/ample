<?php
/**
 * Created by Oleg Popadko
 * Date: 8/12/15
 * Time: 3:28 PM
 */

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        return $menu;
    }
}
