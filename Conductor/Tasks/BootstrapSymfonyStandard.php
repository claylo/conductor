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
        $package = $event->getComposer()->getPackage();
        $vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');
        $install_path = $event
                            ->getComposer()
                            ->getInstallationManager()
                            ->getInstallPath($package);

        $install_path = str_replace(
            DIRECTORY_SEPARATOR . $vendor_dir . DIRECTORY_SEPARATOR . $package->getName(),
            '',
            $install_path
        );
        echo "install path v1: $install_path\n";
        
        // Works, but hacky
        $install_path = realpath(__DIR__ . '/../../../../../');
        echo "install path v2: $install_path\n";
    }
}