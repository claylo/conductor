<?php
/**
 * This file is part of Conductor
 *
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://claylo.mit-license.org/2012/ MIT License
 */
namespace Conductor\Tasks;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class BootstrapSymfonyStandardEdition
{
    protected static $event;
    
    /**
     * After the install event, attempt to install the Symfony Standard Edition
     * files at the same level as my composer.json file.
     * 
     * This enables one-step composer installation without relying on Composer's
     * create-project mechanism. 
     * 
     * You may not want to rely on Composer's create project mechanism for 
     * new Symfony projects IF you already know that you want some additional 
     * capabilities that aren't bundled into the Symfony Standard Edition,
     * such as Doctrine Migrations or DataFixtures.
     * 
     * Using this post-install-cmd in your project's custom composer.json 
     * allows you the convenience of a create-project installation, along
     * with the flexibility to set up the environment the way you want it
     * from the outset.
     * 
     * This is *particularly* useful if you're testing Symfony-related 
     * bundles and modules, and want to quickly set up and tear down 
     * sample Symfony deployments using your complimentary bundles.
     * 
     * @param object $event Composer\Script\Event
     */
    public static function postInstall(Event $event)
    {
        static::$event = $event;
        
        // hacky but reliable        
        $install_path = realpath(__DIR__ . '/../../../../../');
        
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
            $io->write('<error>'.__METHOD__.": $standard_dir not found, can't set up Symfony Standard Edition</error>");
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