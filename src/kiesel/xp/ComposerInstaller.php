<?php namespace kiesel\xp;

use \DirectoryIterator;
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
    $this->io->write('    Installing '.$package->getPrettyName());

    // Find .pth files in added package
    foreach (new DirectoryIterator($base) as $file) {
      if ('.pth' !== substr($file->getFilename(), -4)) continue;

      $this->mergePth($package, $base, $file->getPathname());
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

  protected function mergePth(PackageInterface $package, $base, $from) {
    if (file_exists(self::PTHFILE)) {
      $src= file(self::PTHFILE);
    } else {
      $src= array();
    }

    $mrg= file($from);

    $this->io->write('     Merging '.$from);
    $entries= $this->mergedContents($package, $base, $src, $mrg);

    file_put_contents(self::PTHFILE, implode("\n", $entries));
  }

  protected function mergedContents(PackageInterface $package, $base, $src, $add) {

    // Calculate shortest path to base
    $prefix= $this->filesystem->findShortestPath(realpath('.'), $base, TRUE);
    $add= $this->linesFrom($package, $prefix, $add);
    foreach ($add as $line) {
      $src[]= $add;
    }

    return $src;
  }

  protected function linesFrom(PackageInterface $package, $prefix, $add) {
    $src= array();

    $src[]= sprintf('# Entries added from composer %s (%s)', $package->getId(), $package->getPrettyName());
    foreach ($add as $line) {
      if (!strlen(trim($line))) continue;
      if ('#' == $line{0}) continue;

      $ref= $this->filesystem->normalizePath($prefix.DIRECTORY_SEPARATOR.$line);
      $src[]= $ref;
    }

    return $src;
  }
}