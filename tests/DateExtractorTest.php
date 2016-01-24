<?php

namespace legolasbo\DateExtractor\tests;

use legolasbo\DateExtractor\DateExtractor;
use PHPUnit_Framework_TestCase;

class DateExtractorTest extends PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function emptyStringReturnsNoExtractedDate() {
    $extractor = new DateExtractor('');
    $this->assertFalse($extractor->containsDate());
  }
}
