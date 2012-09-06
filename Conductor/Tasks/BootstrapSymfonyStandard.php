<?php
/**
 * This file is part of Conductor
 *
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Conductor\Tasks;

use Composer\Script\Event;

class BootstrapSymfonyStandard
{
    public static function postInstall(Event $event)
    {
        // $package = $event->getComposer()->getPackage();
        // $install_path = $event
        //                     ->getComposer()
        //                     ->getInstallationManager()
        //                     ->getInstallPath($package);

        $install_path = realpath(__DIR__ . '/../../../../../');
        echo "install path: $install_path\n";
    }
}