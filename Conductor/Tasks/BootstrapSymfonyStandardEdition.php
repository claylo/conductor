<?php
/**
 * This file is part of Conductor
 *
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Conductor\Tasks;

use Composer\Script\Event;

class BootstrapSymfonyStandardEdition
{
    protected static $event;
    
    public static function postInstall(Event $event)
    {
        static::$event = $event;
        
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
        
        static::installSymfonyStandard($install_path);
    }
    
    protected static function installSymfonyStandardEdition($top_level)
    {
        $vendor_dir = static::$event->getComposer()->getConfig()->get('vendor-dir');
        
        $standard_dir = $top_level 
                           . DIRECTORY_SEPARATOR 
                           . $vendor_dir
                           . DIRECTORY_SEPARATOR
                           . 'symfony'
                           . DIRECTORY_SEPARATOR
                           . 'framework-standard-edition';
                                   
        if (! is_dir($standard_dir)) {
            // didn't get it, so do nothing
            return;
        }
        
        
    }
}