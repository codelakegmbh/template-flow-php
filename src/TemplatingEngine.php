<?php

  namespace CodeLake\TemplateFlow;

  class TemplatingEngine {
    protected $template;
    protected $data = [];
    protected static $pipe_classes = [];

    /**
     * Fetches the first match of the requested pipe function.
     * Returns null, if no fitting pipe function has been found.
     *
     * @param string $name
     *
     * @return \Closure|null
     */
    static function pipe_fetch(string $name): ?\Closure {
      $pipe_function = null;
      foreach (self::$pipe_classes as $pipe_class) {
        if (method_exists($pipe_class, $name)) {
          $pipe_function = [$pipe_class, $name];
          break;
        }
      }
      if (!$pipe_function) {
        return null;
      }
      return \Closure::fromCallable($pipe_function);
    }

    /**
     * Factory method for nicer syntax.
     *
     * @return \CodeLake\TemplateFlow\TemplatingEngine
     */
    static function make(): self {
      return new TemplatingEngine();
    }

    /**
     * Adds a new class to the lookup array for pipeline functions.
     *
     * @param string $class_name
     */
    public static function pipes_register_class(string $class_name) {
      if (!in_array($class_name, self::$pipe_classes)) {
        self::$pipe_classes[] = $class_name;
      }
    }

    /**
     * Removes a class from the lookup array for pipeline functions.
     *
     * @param string $class_name
     */
    public static function pipes_unregister_class(string $class_name) {
      unset(self::$pipe_classes[$class_name]);
    }

    /**
     * Sets the template of the current TemplatingEngine.
     *
     * @param string $template
     *
     * @return \CodeLake\TemplateFlow\TemplatingEngine
     */
    function set_template(string $template): self {
      $this->template = $template;
      return $this;
    }

    /**
     * Sets the data of the current TemplatingEngine to fill in.
     *
     * @param array $data
     *
     * @return \CodeLake\TemplateFlow\TemplatingEngine
     */
    function set_data(array $data): self {
      $this->data = $data;
      return $this;
    }

    /**
     * Renders the current TemplatingEngine template with the given data.
     *
     * @return string
     */
    function render(): string {
      return preg_replace_callback(
        TemplatePlaceholder::PLACEHOLDER_REGEX,
        function ($val) {
          if (isset($this->data[$val[1]])) {
            $placeholder = new TemplatePlaceholder($val[0]);
            return $placeholder->process($this->data[$val[1]]);
          } else {
            return $val[0];
          }
        },
        $this->template
      );
    }
  }
