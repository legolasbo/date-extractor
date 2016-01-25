<?php

namespace legolasbo\DateExtractor;

use DateTime;

class DateExtractor {

  /** @var string */
  private $textToSearch;

  /** @var string */
  private $regex = '(\b(\d{1,2}|\d{4})[-| |\/](\d{1,2})[-| |\/](\d{4}|\d{1,2})\b)';

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
    return !empty($this->getDateAsArray());
  }

  /**
   * @return int
   */
  public function numberOfDates() {
    if (preg_match_all($this->regex, $this->textToSearch, $matches)) {
      return count($matches[0]);
    }
    return 0;
  }

  /**
   * @return \DateTime
   */
  public function getDateTimeObject() {
    $result = new DateTime();

    if ($date = $this->getDateAsArray()) {
      $result->setDate($date['year'], $date['month'], $date['day']);
      $result->setTime(0,0);
    }

    return $result;
  }

  /**
   * @return array
   */
  public function getDateAsArray() {
    $result = [];

    if (preg_match($this->regex, $this->textToSearch, $matches)) {
      $result['year'] = $matches[3];
      $result['month'] = $matches[2];
      $result['day'] = $matches[1];
    }

    return $this->ensureValidDate($result);
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

  /**
   * @param array $result
   * @return array
   */
  private function ensureValidDate(array $result) {
    if (empty($result)) {
      return $result;
    }

    if (strlen($result['day']) > 2 && strlen($result['year']) <= 2) {
      $result = $this->swapValuesOfKeys($result, 'day', 'year');
    }

    if ($result['month'] > 12) {
      $result = $this->swapValuesOfKeys($result, 'day', 'month');
    }

    return $result;
  }

  /**
   * @param array $result
   * @return array
   */
  private function swapValuesOfKeys(array $result, $key1, $key2) {
    $temp = $result[$key1];
    $result[$key1] = $result[$key2];
    $result[$key2] = $temp;

    return $result;
  }
}
