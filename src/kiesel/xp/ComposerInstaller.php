<?php namespace kiesel\xp;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class ComposerInstaller extends LibraryInstaller {

  public function supports($packageType) {
    return 'xp-library' === $packageType;
  }

  public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Let parent do regular work
    parent::install($repo, $package);

    // Update project's .pth file
    $base= $this->getPackageBasePath($package);
    $this->io->write('    Updating .pth file w/ paths in '.$base);

    // Detect root directory
    $this->io->write('    Root directory is '.$this->composer->getConfig()->get('home'));

  }

  public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {

    // Let parent do regular work
    parent::update($repo, $initial, $target);

  }

  public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Let parent do regular work
    parent::install($repo, $package);
  }
}