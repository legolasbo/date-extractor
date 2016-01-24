<?php

namespace legolasbo\DateExtractor\tests;

use DateTime;
use legolasbo\DateExtractor\DateExtractor;
use PHPUnit_Framework_TestCase;

class DateExtractorTest extends PHPUnit_Framework_TestCase {

  /**
   * @test
   */
  public function singleDateExpectedToBeNotFound() {
    $this->assertNoDateFound('');
    $this->assertNoDateFound('llalala31-01-2015');
    $this->assertNoDateFound('llalala31-01-2015sgsdgs');
    $this->assertNoDateFound('31-01-2015sgsdgs');
  }

  /**
   * @test
   */
  public function singleDateExtractedCorrectly() {
    $this->assertResultForSingleDatePresent('31-01-2015', '31-01-2015');
    $this->assertResultForSingleDatePresent('01-31-2015', '31-01-2015');
    $this->assertResultForSingleDatePresent('31-1-2015', '31-01-2015');
    $this->assertResultForSingleDatePresent('1-31-2015', '31-01-2015');
    $this->assertResultForSingleDatePresent('2015-01-31', '31-01-2015');
    $this->assertResultForSingleDatePresent('2015-31-01', '31-01-2015');
    $this->assertResultForSingleDatePresent('hello 2015-31-01', '31-01-2015');
    $this->assertResultForSingleDatePresent('hello 2015-31-01 there', '31-01-2015');
    $this->assertResultForSingleDatePresent('31/01/2015', '31-01-2015');
    $this->assertResultForSingleDatePresent('31 01 2015', '31-01-2015');
  }

  public function assertNoDateFound($text) {
    $extractor = new DateExtractor($text);
    $this->assertFalse($extractor->containsDate());
  }

  /**
   * @param $text
   * @param $expected_date
   */
  public function assertResultForSingleDatePresent($text, $expected_date) {
    $extractor = new DateExtractor($text);

    $expected = [
      'year' => 2015,
      'month' => 1,
      'day' => 31,
    ];
    $this->assertTrue($extractor->containsDate());
    $this->assertEquals($expected, $extractor->getDateAsArray());
    $this->assertEquals(new DateTime($expected_date), $extractor->getDateTimeObject());
  }

}
