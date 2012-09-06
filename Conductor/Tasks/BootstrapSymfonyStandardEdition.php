<?php
/**
 * This file is part of Conductor
 *
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://opensource.org/licenses/MIT MIT License
 */
namespace Conductor\Tasks;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

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
        
        static::installSymfonyStandardEdition($install_path);
    }
    
    /**
     * Copy over the necessary files from the framework-standard-edition,
     * only if the directories are non-existent.
     */
    protected static function installSymfonyStandardEdition($top_level)
    {
        $fs = new Filesystem();
        $io = static::$event->getIO();
        
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
        
        $io->write('<info>Installing Symfony Standard Edition files</info>');
        
        /**
         * use symfony-tuned .gitignore?
         */
        if (! file_exists($top_level . DIRECTORY_SEPARATOR . '.gitignore')) {
            try {
                $fs->copy(
                    $standard_dir . DIRECTORY_SEPARATOR . '.gitignore',
                    $top_level . DIRECTORY_SEPARATOR . '.gitignore'
                );
                $io->write('<comment> - Symfony .gitignore copied</comment>');
            } catch (IOException $e) {
                $io->write('<comment>'.__METHOD__.': '.$e->getMessage().'</comment>');
            }
        } else {
            $io->write('<comment> - Symfony .gitignore SKIPPED</comment>');
        }
        
        /**
         * Full copy of app, src, web dirs?
         */
        $to_mirror = array('app', 'src', 'web');
        foreach ($to_mirror as $dir) {
            if (! is_dir($top_level . DIRECTORY_SEPARATOR . $dir)) {
                try {
                    $fs->mirror(
                        $standard_dir . DIRECTORY_SEPARATOR . $dir,
                        $top_level . DIRECTORY_SEPARATOR . $dir
                    );
                    $io->write('<comment> - '.$dir.'/ installed</comment>');
                } catch (IOException $e) {
                    $io->write('<comment>'.__METHOD__.': '.$e->getMessage().'</comment>');
                }
            } else {
                $io->write('<comment> - '.$dir.'/ already exists, skipping</comment>');
            }
        }

    }
}