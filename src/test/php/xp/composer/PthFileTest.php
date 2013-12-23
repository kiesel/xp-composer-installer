<?php namespace xp\composer;

use \PHPUnit_Framework_TestCase;

class PthFileTest extends PHPUnit_Framework_TestCase {

  public function testCreate() {
    new PthFile();
  }

  public function testAddEntry() {
    $pth= new PthFile();
    $pth->addEntry('foobar');

    $this->assertEquals(array('foobar'), $pth->getEntries());
  }

  public function testAddEntry_does_unique() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->addEntry('foobar');

    $this->assertEquals(array('foobar'), $pth->getEntries());
  }

  public function testAddEntry_does_add_2nd() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->addEntry('foobar2');

    $this->assertEquals(array('foobar', 'foobar2'), $pth->getEntries());
  }

  public function testRemoveEntry() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->removeEntry('foobar');

    $this->assertEquals(array(), $pth->getEntries());
  }

  public function testRemoveEntry_reorders_lines() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->addEntry('foobar2');
    $pth->removeEntry('foobar');

    $this->assertEquals(array('foobar2'), $pth->getEntries());
  }

  public function testMergeIn() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->addEntry('foobar2');
    $other= new PthFile();
    $other->addEntry('bla');

    $pth->mergeIn($other);

    $this->assertEquals(array('foobar', 'foobar2', 'bla'), $pth->getEntries());
  }

  public function testSubstract() {
    $pth= new PthFile();
    $pth->addEntry('foobar');
    $pth->addEntry('foobar2');
    $pth->addEntry('bla');

    $other= new PthFile();
    $other->addEntry('foobar2');

    $pth->substract($other);

    $this->assertEquals(array('foobar', 'bla'), $pth->getEntries());
  }


}