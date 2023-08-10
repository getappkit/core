<?php

declare(strict_types=1);

namespace Appkit\Core;

use Appkit\Toolkit\Tpl;
use Exception;

use Throwable;

/**
 * Dispatch
 *
 * @package   Appkit Core
 * @author    Maarten Thiebou
 * @copyright Modufolio
 * @license   https://opensource.org/licenses/MIT
 */
class Dispatch
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public static function actionController($controller, $action, $params = [])
    {
        $controller = '\Appkit\\Controllers\\' . $controller. 'Controller';

        if (!class_exists($controller)) {
            return Tpl::load(Roots::ERRORS . '/404.php');
        }

        $method= self::getMethodFromAction($action);

        $controller_object = new $controller();

        if (!is_callable([$controller_object, $method])) {
            return Tpl::load(Roots::ERRORS . '/404.php');
        }

        return $controller_object->$method($params);
    }

    public static function getMethodFromAction(string $action): string
    {
        return lcfirst($action) . 'Action';
    }
}
