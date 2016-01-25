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
    $this->assertNoDateFound('31 janu 2015');
    $this->assertNoDateFound('janu 31 2015');
  }

  /**
   * @test
   */
  public function singleNumericDateExtractedCorrectly() {
    $this->assertResultForSingleDatePresent('31-01-2015');
    $this->assertResultForSingleDatePresent('01-31-2015');
    $this->assertResultForSingleDatePresent('31-1-2015');
    $this->assertResultForSingleDatePresent('1-31-2015');
    $this->assertResultForSingleDatePresent('2015-01-31');
    $this->assertResultForSingleDatePresent('2015-31-01');
    $this->assertResultForSingleDatePresent('hello 2015-31-01');
    $this->assertResultForSingleDatePresent('hello 2015-31-01 there');
    $this->assertResultForSingleDatePresent('31/01/2015');
    $this->assertResultForSingleDatePresent('31 01 2015');
  }

  /**
   * @test
   */
  public function singleAlphanumericDateExtractedCorrectly() {
    $this->assertResultForSingleDatePresent('31 january 2015');
    $this->assertResultForSingleDatePresent('january 31 2015');
    $this->assertResultForSingleDatePresent('31 jan 2015');
    $this->assertResultForSingleDatePresent('jan 31 2015');
    $this->assertResultForSingleDatePresent('31 January 2015');
  }

  /**
   * @param $text
   */
  public function assertNoDateFound($text) {
    $extractor = new DateExtractor($text);
    $this->assertEquals(0, $extractor->numberOfDates());
    $this->assertFalse($extractor->containsDate());
  }

  /**
   * @param $text
   * @internal param $expected_date
   */
  public function assertResultForSingleDatePresent($text) {
    $extractor = new DateExtractor($text);

    $expected_date = '31-01-2015';
    $expected = [
      'year' => 2015,
      'month' => 1,
      'day' => 31,
    ];
    $this->assertTrue($extractor->containsDate());
    $this->assertEquals(1, $extractor->numberOfDates());
    $this->assertEquals($expected, $extractor->getDateAsArray());
    $this->assertEquals([$expected], $extractor->getDatesAsArray());
    $this->assertEquals(new DateTime($expected_date), $extractor->getDateTimeObject());
  }

}
