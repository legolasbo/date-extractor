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
    $this->assertNoDateFound('31 february 2015 can never exist');
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
    $extractor = new DateExtractor('first date 31-01-2015 and second date 02-27-2014');
    $expected = [
      [
        'year' => 2015,
        'month' => 1,
        'day' => 31,
      ],
      [
        'year' => 2014,
        'month' => 2,
        'day' => 27,
      ],
    ];

    $this->assertEquals(2, $extractor->numberOfDates());
    $this->assertEquals($expected, $extractor->getDatesAsArray());
    $this->assertEquals([new DateTime('31-01-2015'), new DateTime('27-02-2014')], $extractor->getDatesAsDatetimeObjects());
  }

  /**
   * @test
   */
  public function PartialDateExpectedNotToBeFound() {
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
   * @test
   */
  public function multiplePartialDatesGetExtracted() {
    $extractor = new DateExtractor('Partial date with just a year: 2013 and another with a month and year: February 1999');
    $extractor->containsPartialDate();
    $expected = [
      ['year' => 2013],
      [
        'year' => 1999,
        'month' => 2,
      ],
    ];

    $this->assertEquals($expected, $extractor->getPartialDatesAsArray());
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
    $this->assertEquals([], $extractor->getDateAsArray());
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
