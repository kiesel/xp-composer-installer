<?php namespace kiesel\xp\composer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;

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
  }
}