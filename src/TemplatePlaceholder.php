<?php
  namespace CodeLake\TemplateFlow;

  use League\Pipeline\Pipeline;
  use League\Pipeline\PipelineBuilder;

  // TODO renamed pipes, so the name is less ambiguous
  class TemplatePlaceholder {
    const PLACEHOLDER_REGEX    = '/\{\{\s*([a-z-_]+)((\s*\|\s*([a-z-_]+)(\([^\)]+\))?)*)\s*\}\}/';
    const PIPE_SEPARATOR_REGEX = '/\s*\|\s*/';
    const PARAMETERIZED_PIPE_REGEX = '/([a-z-_]+)(\([^\)]+\))/';

    private $_name;
    /** @var string[] */
    private $_pipes = [];
    /** @var \League\Pipeline\Pipeline */
    private $pipe;

    /**
     * TemplatePlaceholder constructor.
     *
     * @param string $placeholder_string
     *
     * @throws \CodeLake\TemplateFlow\UnknownPipeException
     */
    public function __construct(string $placeholder_string) {
      if (!preg_match(self::PLACEHOLDER_REGEX, $placeholder_string)) {
        throw new \InvalidArgumentException('Malformatted placeholder string!');
      }

      $result = [];
      preg_match_all(self::PLACEHOLDER_REGEX, $placeholder_string, $result);
      if ($pipes_string = $result[2][0]) {
        $pipes = preg_split('/\s*\|\s*(?![^\(]*\))/', $pipes_string);
        array_splice($pipes, 0, 1);
        $this->_pipes = $pipes;
        unset($pipes);
      }

      $this->_name = $result[1][0];
      unset($result);

      $this->pipe = self::build_templating_pipe($this->pipes());
    }

    /**
     * Tries to create a TemplatePlaceholder instance.
     * Returns a proper instance on success, otherwise null.
     *
     * @param string $placeholder_string
     *
     * @return \CodeLake\TemplateFlow\TemplatePlaceholder|null
     */
    static function create_or_null(string $placeholder_string): ?TemplatePlaceholder {
      try {
        return new TemplatePlaceholder($placeholder_string);
      } catch (\InvalidArgumentException $e) {
        return null;
      } catch (UnknownPipeException $e) {
        return null;
      }
    }

    /**
     * Takes an array of templating pipeline functions and transforms them into and actual Pipeline.
     *
     * @param array $pipes The template pipeline functions to include in the resulting Pipeline.
     *
     * @return \League\Pipeline\Pipeline
     * @throws \CodeLake\TemplateFlow\UnknownPipeException
     */
    static function build_templating_pipe(array $pipes): Pipeline {
      $pipe_builder = new PipelineBuilder();
      foreach ($pipes as $pipe_name) {
        $pipe_matches = [];
        if (preg_match(self::PARAMETERIZED_PIPE_REGEX, $pipe_name, $pipe_matches)) {
          $pipe_name = $pipe_matches[1];
          $parameter_string = substr($pipe_matches[2], 1, -1);
          $parameters = preg_split(self::PIPE_SEPARATOR_REGEX, $parameter_string);

          try {
            $pipe_function = TemplatingEngine::pipe_fetch($pipe_name)(...$parameters);
          } catch (\TypeError $e) {
            throw new InvalidPipeParameterException("Invalid arguments for pipe '{$pipe_name}'!", -1, $e);
          }
        } else {
          $pipe_function = TemplatingEngine::pipe_fetch($pipe_name);
        }
        try {
          $pipe_builder->add($pipe_function);
        } catch (\TypeError $e) {
          throw new UnknownPipeException("The pipe '{$pipe_name}' is unknown! Maybe you forgot to register it?", -1, $e);
        }
      }

      return $pipe_builder->build();
    }

    function name(): string {
      return $this->_name;
    }

    function pipes(): array {
      return $this->_pipes;
    }

    /**
     * Computes the outcome of the TemplatePlaceholder.
     *
     * @param $value
     *
     * @return mixed
     */
    function process($value) {
      $processed = $this->pipe->process($value);
      if ($processed instanceof RawOutput) {
        return (string)$processed;
      }
      return htmlspecialchars($processed);
    }
  }
