<?php
/**
 * This file is part of Conductor.
 *
 * (c) Clay Loveless <clay@php.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Conductor;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Util\Filesystem;

class ScriptInstaller extends LibraryInstaller
{
    protected $conductorDir;

    /**
     * Initializes Conductor installer
     *
     * @todo  Allow conductorDir to be set by 'extra' configuration value?
     * @see   http://getcomposer.org/doc/04-schema.md#extra
     * @param IOInterface $io
     * @param Composer $composer
     * @param string $type
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library')
    {
        parent::__construct($io, $composer, $type);
        $this->conductorDir = 'conductor';
    }

    /**
     * {@inheritDoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        return $this->conductorDir . '/' . $package->getPrettyName();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return 'conductor-script' === $packageType;
    }
}