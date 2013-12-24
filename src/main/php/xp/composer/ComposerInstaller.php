<?php namespace xp\composer;

use \DirectoryIterator;
use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class ComposerInstaller extends LibraryInstaller {
  const TYPE    = 'xp-library';
  const PTHFILE = 'composer.pth';
  const WHSP    = '    ';

  public function supports($packageType) {
    return self::TYPE === $packageType;
  }

  public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Let parent do regular work
    parent::install($repo, $package);

    // Add new dependencies
    $this->addDependencyToPth($package);
  }

  public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target) {

    // Remove dependencies when uninstalling
    $this->removeDependencyFromPth($initial);

    // Let parent do regular work
    parent::update($repo, $initial, $target);

    // Add new dependencies
    $this->addDependencyToPth($target);
  }

  public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package) {

    // Remove dependencies when uninstalling
    $this->removeDependencyFromPth($package);

    // Let parent do regular work
    parent::uninstall($repo, $package);
  }

  protected function addDependencyToPth(PackageInterface $package) {

    // Update project's .pth file
    $base= $this->getPackageBasePath($package);

    // Find .pth files in added package
    foreach (new DirectoryIterator($base) as $file) {
      if ('.pth' !== substr($file->getFilename(), -4)) continue;

      $this->mergePth($package, $base, $file->getPathname());
    }
  }

  protected function removeDependencyFromPth(PackageInterface $package) {

    // Update project's .pth file
    $base= $this->getPackageBasePath($package);

    // Find .pth files in added package
    foreach (new DirectoryIterator($base) as $file) {
      if ('.pth' !== substr($file->getFilename(), -4)) continue;

      $this->unmergePth($package, $base, $file->getPathname());
    }
  }

  protected function mergePth(PackageInterface $package, $base, $from) {
    $src= new PthFile();
    if (file_exists(self::PTHFILE)) {
      $src->load(self::PTHFILE);
    }

    $mrg= new PthFile();
    $mrg->load($from);

    $this->io->write(self::WHSP.'Merging '.$from);
    $this->mergedContents($package, $base, $src, $mrg);
    $src->save(self::PTHFILE);
  }

  protected function unmergePth(PackageInterface $package, $base, $from) {
    $src= new PthFile();
    if (file_exists(self::PTHFILE)) {
      $src->load(self::PTHFILE);
    }

    $mrg= new PthFile();
    $mrg->load($from);

    $this->io->write(self::WHSP.'Unmerging '.$from);
    $this->unmergedContents($package, $base, $src, $mrg);
    $src->save(self::PTHFILE);
  }

  protected function mergedContents(PackageInterface $package, $base, PthFile $src, PthFile $add) {

    // Calculate shortest path to base
    $prefix= $this->filesystem->findShortestPath(realpath('.'), $base, TRUE);
    $src->mergeIn($this->rewriteLinesFrom($package, $prefix, $add));

    return $src;
  }

  protected function unmergedContents(PackageInterface $package, $base, PthFile $src, PthFile $sub) {

    // Calculate shortest path to base
    $prefix= $this->filesystem->findShortestPath(realpath('.'), $base, TRUE);
    $src->substract($this->rewriteLinesFrom($package, $prefix, $sub));

    return $src;
  }

  protected function rewriteLinesFrom(PackageInterface $package, $prefix, PthFile $add) {
    $pth= new PthFile();

    $pth->addEntry($this->commentFor($package));
    foreach ($add->getEntries() as $line) {

      // Keep comments & empty lines as is
      if (
        !strlen(trim($line)) ||
        '#' == $line{0}
      ) {
        $pth->addEntry($line);
      }

      $ref= $this->filesystem->normalizePath($prefix.DIRECTORY_SEPARATOR.$line);
      $pth->addEntry($ref);
    }

    return $pth;
  }

  protected function commentFor(PackageInterface $package) {
    return sprintf('# Entries added by composer for %s', $package->getPrettyName());
  }
}