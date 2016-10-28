<?php
/*
 * This file is part of the Recommend Product plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (function_exists('log_info') === false) {

    function log_emergency($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->emergency($message, $context);
    }

    function log_alert($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->alert($message, $context);
    }

    function log_critical($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->critical($message, $context);
    }

    function log_error($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->error($message, $context);
    }

    function log_warning($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->warning($message, $context);
    }

    function log_notice($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->notice($message, $context);
    }

    function log_info($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->info($message, $context);
    }

    function log_debug($message, array $context = array())
    {
        $GLOBALS['eccube.logger']->debug($message, $context);
    }

    function eccube_log_init($app)
    {
        if (isset($GLOBALS['eccube.logger'])) {
            return;
        }
        $GLOBALS['eccube.logger'] = $app['monolog'];
        $app['eccube.monolog.factory'] = $app->protect(function ($config) use ($app) {
            return $app['monolog'];
        });
    }
}
// 3.0.9以上の場合は初期化処理を行う.
if (method_exists('Eccube\Application', 'getInstance')) {
    $app = \Eccube\Application::getInstance();
    eccube_log_init($app);
}