<?php
  /**
   * Created by PhpStorm.
   * User: joernneumeyer
   * Date: 05.12.18
   * Time: 08:52
   */

  namespace CodeLake\TemplateFlow;


  class RawOutput {
    /** @var string */
    private $value;

    function __construct(string $value) {
      $this->value = $value;
    }

    function get_value(): string {
      return $this->value;
    }

    public function __toString(): string {
      return $this->value;
    }
  }