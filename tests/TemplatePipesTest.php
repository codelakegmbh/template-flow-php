<?php
  /**
   * Created by PhpStorm.
   * User: joernneumeyer
   * Date: 05.12.18
   * Time: 10:44
   */

  namespace Tests\Unit;


  use CodeLake\TemplateFlow\TemplatePipes;
  use PHPUnit\Framework\TestCase;

  class TemplatePipesTest extends TestCase {
    function testLinkCanProduceWebLinks() {
      $link = TemplatePipes::link('test')('test.com');
      $this->assertEquals('<a href="test.com">test</a>', $link);
    }

    function testLinkCanProduceMailToLinks() {
      $link = TemplatePipes::link('test', 'mail')('test@test.com');
      $this->assertEquals('<a href="mailto:test@test.com">test</a>', $link);
    }

    function testLinkThrowsOnInvalidEmailInEmailLink() {
      $this->expectException(\InvalidArgumentException::class);
      TemplatePipes::link('test', 'mail')('test.com');
    }
  }