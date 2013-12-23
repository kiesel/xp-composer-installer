<?php namespace xp\composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class ComposerInstallerPlugin implements PluginInterface {
  public function activate(Composer $composer, IOInterface $io) {
    $composer->getInstallationManager()->addInstaller(new ComposerInstaller($io, $composer));
  }
}