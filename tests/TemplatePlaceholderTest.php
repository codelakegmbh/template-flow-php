<?php

  namespace Tests\Unit;

  use CodeLake\TemplateFlow\TemplatePlaceholder;
  use CodeLake\TemplateFlow\TemplatingEngine;
  use CodeLake\TemplateFlow\UnknownPipeException;

  class TemplatePlaceholderTest extends \PHPUnit\Framework\TestCase {
    function testThrowsOnInvalidPlaceholderString() {
      $this->expectException(\InvalidArgumentException::class);
      new TemplatePlaceholder('moin');
    }

    function testProperlyRetrievesPlaceholderName() {
      $placeholder = new TemplatePlaceholder('{{moin}}');
      $this->assertEquals('moin', $placeholder->name());
    }

    function testProperlyRetrievesPlaceholderPipes() {
      $placeholder = new TemplatePlaceholder('{{moin|upper}}');
      $this->assertEquals(['upper'], $placeholder->pipes());
    }

    function testFactoryMethodReturnsNullOnInvalidPlaceholderString() {
      $placeholder = TemplatePlaceholder::create_or_null('bla');
      $this->assertNull($placeholder);
    }

    function testFactoryMethodReturnTemplatePlaceholderOnValidInput() {
      $placeholder = TemplatePlaceholder::create_or_null('{{name}}');
      $this->assertInstanceOf(TemplatePlaceholder::class, $placeholder);
    }

    function testThrowsExceptionIfThePipeWasNotFound() {
      $this->expectException(UnknownPipeException::class);
      new TemplatePlaceholder('{{name|wrehbeagthjrwsaerzt}}');
    }

    function testProperlyAppliesDefinedPipes() {
      $placeholder = new TemplatePlaceholder('{{name|capitalize}}');
      $result = $placeholder->process('john');
      $this->assertEquals('John', $result);
    }

    function testAcceptsParameterizedPipes() {
      $works = false;

      try {
        new TemplatePlaceholder('{{name|shorten(4)}}');
        $works = true;
      } catch (\InvalidArgumentException $e) {
      }

      $this->assertTrue($works);
    }

    function testAppliesParameterizedPipeProperly() {
      $placeholder = new TemplatePlaceholder('{{name|shorten(4)}}');
      $name = 'Johnny';
      $this->assertEquals('John', $placeholder->process($name));
    }

    function testProperlyPassesMultipleParametersToParameterizedPipe() {
      $placeholder = new TemplatePlaceholder('{{name|link(web|DuckDuckGo)}}');
      $result = $placeholder->process('www.duckduckgo.com');
      $this->assertEquals('<a href="www.duckduckgo.com">DuckDuckGo</a>', $result);
    }
  }
