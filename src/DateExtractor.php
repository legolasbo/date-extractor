<?php

namespace legolasbo\DateExtractor;

use DateTime;

class DateExtractor {

  private $textToSearch;

  /**
   * DateExtractor constructor.
   */
  public function __construct($textToSearch) {
    $this->textToSearch = $textToSearch;
  }

  /**
   * @return bool
   */
  public function containsDate() {
    return !empty($this->getDateAsArray());
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

    if (preg_match('(\b(\d{1,2}|\d{4}|[a-zA-Z]+)[-| |\/](\d{1,2}|[a-zA-Z]+)[-| |\/](\d{4}|\d{1,2})\b)', $this->textToSearch, $matches)) {
      $result['year'] = $matches[3];
      $result['month'] = $matches[2];
      $result['day'] = $matches[1];
    }

    return $this->ensureValidResult($result);
  }

  /**
   * @param array $result
   * @return array
   */
  private function ensureValidResult(array $result) {
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
