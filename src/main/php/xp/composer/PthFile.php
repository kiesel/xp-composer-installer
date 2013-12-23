<?php namespace xp\composer;

class PthFile {
  protected $lines = array();

  public function __construct() {
  }

  public function load($path) {
    $this->lines= array();

    foreach (file($path) as $line) {
      $this->addEntry($line);
    }
  }

  public function save($path) {
    file_put_contents($path, implode("\n", $this->lines));
  }

  public function addEntry($entry) {
    $entry= trim($entry);
    foreach ($this->lines as $l) {
      if ($l == $entry) return;
    }

    $this->lines[]= $entry;
  }

  public function removeEntry($entry) {
    $entry= trim($entry);
    if (false !== ($pos= array_search($entry, $this->lines))) {
      unset($this->lines[$pos]);
      $this->lines= array_values($this->lines);
      return true;
    }

    return false;
  }

  public function mergeIn(PthFile $other) {
    foreach ($other->lines as $line) {
      $this->addEntry($line);
    }
  }

  public function substract(PthFile $other) {
    foreach ($other->lines as $line) {
      $this->removeEntry($line);
    }
  }

  public function getEntries() {
    return $this->lines;
  }
}