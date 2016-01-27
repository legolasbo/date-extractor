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
    $this->assertResultForSingleDatePresent('31/01/2015');
    $this->assertResultForSingleDatePresent('31/1/2015');
    $this->assertResultForSingleDatePresent('31 01 2015');
    $this->assertResultForSingleDatePresent('31 1 2015');
    $this->assertResultForSingleDatePresent('2015-01-31');
    $this->assertResultForSingleDatePresent('2015-31-01');
    $this->assertResultForSingleDatePresent('hello 2015-31-01');
    $this->assertResultForSingleDatePresent('hello 2015-31-01 there');
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
   * @test
   */
  public function multipleDatesGetExtracted() {
    $extractor = new DateExtractor('first date 31-01-2015 and second date 02-30-2014');
    $expected = [
      [
        'year' => 2015,
        'month' => 1,
        'day' => 31,
      ],
      [
        'year' => 2014,
        'month' => 2,
        'day' => 30,
      ],
    ];

    $this->assertEquals(2, $extractor->numberOfDates());
    $this->assertEquals($expected, $extractor->getDatesAsArray());
    $this->assertEquals([new DateTime('31-01-2015'), new DateTime('30-02-2014')], $extractor->getDatesAsDatetimeObjects());
  }

  /**
   * @test
   */
  public function singlePartialDateExpectedNotToBeFound() {
    $this->assertNoPartialDate('');
    $this->assertNoPartialDate('no partial date present');
    $this->assertNoPartialDate('some numbers 12 present but no date');
    $this->assertNoPartialDate('The date 15 oct 1999 is a full date and should therefor not be considered partial');
    $this->assertNoPartialDate('some numbers with various digits 1, 22, 333, 55555');
  }

  /**
   * @test
   */
  public function partialDateGetsExtracted() {
    $this->assertSinglePartialDateWithMonth('01-2015');
    $this->assertSinglePartialDateWithMonth('january 2015');
    $this->assertSinglePartialDateWithMonth('Partial date january 2015 within text');
    $this->assertSingleYear('2015');
    $this->assertSingleYear('Single year 2015 within text');
    $this->assertSingleYear('Single year at end of sentence 2015.');

  }

  /**
   * @param $text
   */
  public function assertNoPartialDate($text) {
    $extractor = new DateExtractor($text);
    $this->assertFalse($extractor->containsPartialDate());
  }

  /**
   * @param $text
   */
  public function assertSinglePartialDateWithMonth($text) {
    $extractor = new DateExtractor($text);
    $expected = [
      'year' => 2015,
      'month' => 1,
    ];
    $this->assertTrue($extractor->containsPartialDate());
    $this->assertEquals($expected, $extractor->getPartialDate());
  }

  /**
   * @param $text
   */
  public function assertSingleYear($text) {
    $extractor = new DateExtractor($text);
    $expected = [
      'year' => 2015,
    ];
    $this->assertTrue($extractor->containsPartialDate());
    $this->assertEquals($expected, $extractor->getPartialDate());
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
   */
  public function assertResultForSingleDatePresent($text) {
    $extractor = new DateExtractor($text);

    $expected = [
      'year' => 2015,
      'month' => 1,
      'day' => 31,
    ];
    $this->assertTrue($extractor->containsDate());
    $this->assertEquals(1, $extractor->numberOfDates());
    $this->assertEquals($expected, $extractor->getDateAsArray());
    $this->assertEquals([$expected], $extractor->getDatesAsArray());
    $this->assertEquals(new DateTime('31-01-2015'), $extractor->getDateTimeObject());
  }

}
