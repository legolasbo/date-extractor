<?php

namespace legolasbo\DateExtractor;

class DateExtractor {

  private $textToSearch;

  /**
   * DateExtractor constructor.
   */
  public function __construct($textToSearch) {
    $this->textToSearch = $textToSearch;
  }

  public function containsDate() {
    return FALSE;
  }
}
