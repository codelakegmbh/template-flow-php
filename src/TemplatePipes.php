<?php

  namespace CodeLake\TemplateFlow;

  final class TemplatePipes {
    static function capitalize(string $value): string {
      return ucfirst($value);
    }

    static function link(string $display, string $type = 'web'): \Closure {
      return function (string $value) use ($type, $display) {
        $link = '';
        switch ($type) {
          case 'mail':
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
              throw new \InvalidArgumentException("The email '{$value}' is invalid!");
            }
            $link = '<a href="mailto:' . $value . '">' . $display . '</a>';
            break;
          case 'web':
            $link = '<a href="' . $value . '">' . $display . '</a>';
            break;
          default:
            throw new \InvalidArgumentException("Unknown/unsupported link type '{$type}'!");
        }
        return new RawOutput($link);
      };
    }

    static function lower(string $value): string {
      return strtolower($value);
    }

    static function raw(string $value): RawOutput {
      return new RawOutput($value);
    }

    static function shorten(int $length): \Closure {
      return function (string $value) use ($length) {
        return substr($value, 0, $length);
      };
    }

    static function trim(string $value): string {
      return trim($value);
    }

    static function trim_left(string $value): string {
      return ltrim($value);
    }

    static function trim_right(string $value): string {
      return rtrim($value);
    }

    static function upper(string $value): string {
      return strtoupper($value);
    }
  }
