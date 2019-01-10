TemplateFlow
---
TemplateFlow is a simple and extendable templating engine
with a focus on E-Mail and HTML templating.
Therefore, TemplateFlow may not work as expected if you want
to output plain text which contains HTML special characters like ```<```.
It provides an easy way to define and placeholders in a template
and process them via pipes.
### Template
In TemplateFlow a simple template can be defined as follows ```Hello {{name}}!```.
Rendering that template with the desired data can be achieved as follows:
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
$engine->set_template('Hello {{name}}!');
$engine->set_data(['name' => 'foo']);
$result = $engine->render(); // evaluates to 'Hello foo!'
```
### Pipes
Replacing placeholders with values is already nice to have
but not really powerful. Therefore, TemplateFlow provides
pipes. Pipes allow you to specify how a given placeholder
value should be mutated before it is displayed.
TemplateFlow already comes with a lot of predefined pipes.
A complete list of the predefined pipes can be found at
[Predefined Pipes](#predefined-pipes)
```php
use CodeLake\TemplateFlow\TemplatingEngine;
$engine = new TemplatingEngine();
$engine->set_template('Hello {{name|capitalize}}!');
$engine->set_data(['name' => 'foo']);
$result = $engine->render(); // evaluates to 'Hello Foo!'
```
There are also parameterized pipes - like ``shorten`` -
which take a parameter. A parameter may be passed to a pipe
in parenthesis.
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
$engine->set_template('Welcome {{name|shorten(4)}}.');
$engine->set_data(['name' => 'Johnny']);
$result = $engine->render();
// produces 'Welcome John.'
```
Some pipes also require more than one parameter.
To distinct multiple parameters, the pipe operator (``|``) is used.
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
// use the capitalize pipe to make the first letter upper case
$engine->set_template('Please fill out our {{survey_link|link(Survey)}}!');
$engine->set_data(['survey_link' => 'www.example-survey.com']);
$result = $engine->render();
// produces anchor '<a href="www.example-survey.com">Survey</a>'
```

### Adding Pipes
Pipes are just functions in a ```class```.
Therefore, if you want to add your own pipes to TemplateFlow, you just have to create a new ```class``` with the desired pipes as methods on it.

```php
use CodeLake\TemplateFlow\TemplatingEngine;

class MyPipes {
  /**
   * Returns the last character of a string.
   */
  static function last(string $value): string {
    return subsctr($value, -1);
  }
}

$engine = new TemplatingEngine();

$engine->pipes_register_class(MyPipes::class);
// all static methods of the class 'MyPipes' are now available as pipes
```

### Predefined Pipes
In order to use predefined pipes, you first have to add it to the engine.
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();

$engine->pipes_register_class(CodeLake\TemplateFlow\TemplatePipes::class);
```

#### capitalize
Mutates the first character of a string to upper case.
#### link
Creates a link (anchor tag) with the specified address.
##### usage - for web links
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
$engine->set_template('Please fill out our {{survey_link|link(Survey)}}!');
$engine->set_data(['survey_link' => 'www.example-survey.com']);
$result = $engine->render();
// produces anchor '<a href="www.example-survey.com">Survey</a>'
```
##### usage - for mailto links
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
$engine->set_template('If you want, contact our {{support_link|link(Support|mail)}}!');
$engine->set_data(['support_link' => 'support@example.com']);
$result = $engine->render();
// produces anchor '<a href="mailto:support@example.com">Support</a>'
```
### lower
Transforms all characters in a string to lower case.
### raw
Returns a ``RawOutput`` instance, so the pipeline result will not be escaped.  
**NOTE** This pipe has to be the last one in the chain. Otherwise the output will be escaped as usual.
### shorten
Cuts off the remaining characters of the pipeline string
after the n-th character.
```php
use CodeLake\TemplateFlow\TemplatingEngine;

$engine = new TemplatingEngine();
$engine->set_template('Welcome {{name|shorten(4)}}.');
$engine->set_data(['name' => 'Johnny']);
$result = $engine->render();
// produces 'Welcome John.'
```
### trim
Removes all whitespace characters from the left and right side of a string.
### trim_left
Removes all whitespace characters from the left side of a string.
### trim_right
Removes all whitespace characters from the right side of a string.
### upper
Transforms all characters in a string to upper case.
