<?php
  /**
   * Created by PhpStorm.
   * User: joernneumeyer
   * Date: 05.12.18
   * Time: 10:44
   */

  namespace Tests\Unit;


  use CodeLake\TemplateFlow\TemplatePipes;
  use CodeLake\TemplateFlow\TemplatingEngine;
  use PHPUnit\Framework\TestCase;

  class TemplatePipesTest extends TestCase {
    static function setUpBeforeClass() {
      TemplatingEngine::pipes_register_class(TemplatePipes::class);
    }

    static function tearDownAfterClass() {
      TemplatingEngine::pipes_unregister_class(TemplatePipes::class);
    }

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