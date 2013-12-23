<?php namespace kiesel\xp;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class ComposerInstaller extends LibraryInstaller {
  const PTHFILE = 'composer.pth';

  public function supports($packageType) {
    return 'xp-library' === $packageType;
  }

  public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Let parent do regular work
    parent::install($repo, $package);

    // Update project's .pth file
    $base= $this->getPackageBasePath($package);
    $this->io->write('    Updating .pth file w/ paths in '.$base);

    // Find .pth files in added package
    foreach (new DirectoryIterator($base) as $file) {
      if ('pth' !== $file->getExtension()) continue;

      $this->mergePth($base, $file->getPathname());
    }
  }

  public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {

    // Let parent do regular work
    parent::update($repo, $initial, $target);

  }

  public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Let parent do regular work
    parent::install($repo, $package);
  }

  protected function mergePth($base, $from) {
    $src= file(self::PTHFILE);
    $mrg= file($from);

    // Calculate shortest path to base
    $prefix= $this->filesystem->findShortestPath(realpath('.'), $base, TRUE);
    $src[]= sprintf('# Entries added from composer %s (%s)', $package->getId(), $package->getPrettyName());
    foreach ($mrg as $line) {
      if (!strlen(trim($line))) continue;
      if ('#' == $line{0}) continue;

      $ref= $prefix.DIRECTORY_SEPARATOR.$line;
      $src[]= $ref;
    }

    file_put_contents(self::PTHFILE, implode("\n", $src));
  }
}