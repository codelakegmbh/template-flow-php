<?php

  namespace Tests\Unit;

  use CodeLake\TemplateFlow\TemplatingEngine;
  use PHPUnit\Framework\TestCase;

  class TemplatingEngineTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    function testProperlyInsertsName() {
      $placeholders = [
        '{{name}}',
        '{{ name}}',
        '{{name }}',
        '{{ name }}',
        '{{           name           }}',
      ];
      $data = ['name' => 'John'];
      foreach ($placeholders as $placeholder) {
        $result = TemplatingEngine::make()
          ->set_template("Hello {$placeholder}!")
          ->set_data($data)
          ->render();
        $this->assertEquals('Hello John!', $result);
      }
    }

    function testPrintsPlaceholderIfNoFittingDataIsProvided() {
      $data = [];
      $result = TemplatingEngine::make()
        ->set_template('Hello {{name}}!')
        ->set_data($data)
        ->render();
      $this->assertEquals('Hello {{name}}!', $result);
    }

    function testTemplatingPipeCanBeFetched() {
      $pipe = TemplatingEngine::pipe_fetch('upper');
      $this->assertTrue(is_callable($pipe));
    }

    function testDoesNotAcceptAnEmptyPipeline() {
      $result = TemplatingEngine::make()
        ->set_template('Hello {{name|}}!')
        ->render();
      $this->assertEquals('Hello {{name|}}!', $result);
    }

    function testReturnsPredefinedPipe() {
      $pipe_function = TemplatingEngine::pipe_fetch('upper');
      $this->assertNotNull($pipe_function);
    }

    function testAlsoLooksForPipesInNewlyAddedTemplatePipeClass() {
      $templating_pipes = new class {
        static function first_only(string $value): string {
          return $value[0];
        }
      };

      $class_name = get_class(new $templating_pipes);

      TemplatingEngine::pipes_register_class($class_name);
      $pipe_function = TemplatingEngine::pipe_fetch('first_only');
      $this->assertTrue(is_callable($pipe_function));
      TemplatingEngine::pipes_unregister_class($class_name);
    }
  }
