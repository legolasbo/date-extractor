<?php

namespace legolasbo\DateExtractor;

use DateTime;

class DateExtractor {

  /** @var string */
  private $textToSearch;

  /** @var string */
  private $regularExpressionForFullDate = '(\b(\d{1,2}|\d{4})\b[-| |\/]\b(\d{1,2})\b[-| |\/]\b(\d{4}|\d{1,2})\b)';

  /** @var string */
  private $regularExpressionForPartialDate = '(\b(\d{0,2})\b[-| |\/]?\b(\d{4})\b)';

  /**
   * DateExtractor constructor.
   */
  public function __construct($text) {
    $this->textToSearch = $this->replaceTextualMonthsWithNumbers($text);
  }

  /**
   * @param string $text
   * @return string
   */
  private function replaceTextualMonthsWithNumbers($text) {
    foreach ($this->getMonthMap() as $month => $number) {
      $text = str_ireplace($month, $number, $text);
    }
    return $text;
  }

  /**
   * @return bool
   */
  public function containsDate() {
    return $this->numberOfDates() > 0;
  }

  /**
   * @return int
   */
  public function numberOfDates() {
    return count($this->getDatesAsArray());
  }

  /**
   * @return array
   */
  public function getDatesAsArray() {
    $dates = [];

    if (preg_match_all($this->regularExpressionForFullDate, $this->textToSearch, $matches)) {
      $matching_dates = $matches[0];
      while ($matching_date = array_shift($matching_dates)) {
        $date = [];
        $date['year'] = array_shift($matches[3]);
        $date['month'] = array_shift($matches[2]);
        $date['day'] = array_shift($matches[1]);

        $date = $this->ensureCorrectlyMapped($date);

        if ($this->isValidDate($date)) {
          $dates[] = $date;

        }
      }
    }

    return $dates;
  }


  /**
   * @param array $date
   * @return array
   */
  private function ensureCorrectlyMapped(array $date) {
    if (strlen($date['day']) > 2 && strlen($date['year']) <= 2) {
      $date = $this->swapValuesOfKeys($date, 'day', 'year');
    }

    if ($date['month'] > 12) {
      $date = $this->swapValuesOfKeys($date, 'day', 'month');
    }

    return $this->ensureDayAndMonthPrefixedWithZero($date);
  }

  /**
   * @param array $result
   * @param $key1
   * @param $key2
   * @return array
   */
  private function swapValuesOfKeys(array $result, $key1, $key2) {
    $temp = $result[$key1];
    $result[$key1] = $result[$key2];
    $result[$key2] = $temp;

    return $result;
  }

  private function ensureDayAndMonthPrefixedWithZero(array $date) {
    foreach (['day', 'month'] as $key) {
      $date[$key] = strlen($date[$key]) === 1 ? '0'.$date[$key] : $date[$key];
    }
    return $date;
  }

  /**
   * @param array $dateArray
   * @return bool
   */
  private function isValidDate(array $dateArray){
    $dateTime = $this->dateArrayToDateTime($dateArray);
    return $dateTime->format('d-m-Y') === "{$dateArray['day']}-{$dateArray['month']}-{$dateArray['year']}";
  }

  /**
   * @param $dateArray
   * @return \DateTime
   */
  private function dateArrayToDateTime($dateArray) {
    $date = new DateTime();
    $date->setDate($dateArray['year'], $dateArray['month'], $dateArray['day']);
    $date->setTime(0,0);
    return $date;
  }

  /**
   * @return \DateTime
   */
  public function getDateTimeObject() {
    $dates = $this->getDatesAsDatetimeObjects();
    return reset($dates);
  }

  /**
   * @return array
   */
  public function getDateAsArray() {
    $dates = $this->getDatesAsArray();
    return array_shift($dates);
  }


  /**
   * @return array
   */
  public function getDatesAsDatetimeObjects() {
    $dates = [];

    foreach ($this->getDatesAsArray() as $dateArray) {
      $dates[] = $this->dateArrayToDateTime($dateArray);
    }

    return $dates;
  }

  /**
   * @return array
   */
  private function getMonthMap() {
    return [
      'january' => 1,
      'february' => 2,
      'march' => 3,
      'april' => 4,
      'may' => 5,
      'june' => 6,
      'july' => 7,
      'august' => 8,
      'september' => 9,
      'october' => 10,
      'november' => 11,
      'december' => 12,
      'jan' => 1,
      'feb' => 2,
      'mar' => 3,
      'apr' => 4,
      'jun' => 6,
      'jul' => 7,
      'aug' => 8,
      'sept' => 9,
      'sep' => 9,
      'oct' => 10,
      'nov' => 11,
      'dec' => 12,
    ];
  }

  public function containsPartialDate() {
    return !empty($this->getPartialDatesAsArray());
  }

  /**
   * @return array
   */
  public function getPartialDatesAsArray() {
    $partialDates = [];

    $textToSearch = $this->removeCompleteDates($this->textToSearch);
    if (preg_match_all($this->regularExpressionForPartialDate, $textToSearch, $matches)) {
      $matchingPartialDates = $matches[0];
      while ($matching_date = array_shift($matchingPartialDates)) {
        $partialDate = [];
        $partialDate['year'] = array_shift($matches[2]);
        $partialDate['month'] = array_shift($matches[1]);
        if (empty($partialDate['month'])) {
          unset($partialDate['month']);
        }

        $partialDates[] = $partialDate;
      }
    }

    return $partialDates;
  }

  /**
   * @return array
   */
  public function getPartialDate() {
    $dates = $this->getPartialDatesAsArray();
    return reset($dates);
  }

  /**
   * @param string $text
   * @return string
   */
  private function removeCompleteDates($text) {
    return preg_replace($this->regularExpressionForFullDate, '', $text);
  }
}
