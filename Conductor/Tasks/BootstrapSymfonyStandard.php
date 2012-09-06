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
        $vendor_dir = $event->getComposer()->getConfig()->get('vendor-dir');
        dirname($vendor_dir);
        echo "install root: $vendor_dir\n";
    }
}