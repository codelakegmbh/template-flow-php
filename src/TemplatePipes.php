<?php

  namespace CodeLake\TemplateFlow;

  use function Couchbase\defaultDecoder;

  final class TemplatePipes {
    static function capitalize(string $value): string {
      return ucfirst($value);
    }

    static function link(string $type, string $display): \Closure {
      return function (string $value) use ($type, $display) {
        switch ($type) {
          case 'mail':
            return '<a href="mailto:'.$value.'">'.$display.'</a>';
          case 'web':
            return '<a href="'.$value.'">'.$display.'</a>';
          default:
            throw new \InvalidArgumentException("Unknown/unsupported link type '{$type}'!");
        }
      };
    }

    static function lower(string $value): string {
      return strtolower($value);
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
