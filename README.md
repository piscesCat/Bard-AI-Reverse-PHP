## Bard-AI-Reverse

Bard-AI-Reverse is a Composer package that allows you to interact with Google Bard AI using PHP code.

## Language

- [English](README.md)
- [Tiếng Việt](README_vi.md)

## Installation

Minimum PHP version required is 7.0

Use [Composer](https://getcomposer.org) to install the package.

Run the following command in the terminal:

```
composer require khaiphan/bard-reverse:dev-main
```

## Usage

1. First, you need to include the autoloader in your PHP code:

```php
require 'vendor/autoload.php';
```

2. Next, create an instance of the `Bard` class and provide cookie Google Bard AI:

```php
use KhaiPhan\Google\Bard;

$bard = new Bard('__Secure-1PSID');
```

Make sure you replace `'__Secure-1PSID'` with the value of the __Secure-1PSID cookie obtained from the [Google Bard AI website](https://bard.google.com).

3. Then, call the `getAnswer()` method to retrieve the response from Bard AI:

```php
$answer = $bard->getAnswer('Hello');
```

4. You can access the answer from Bard AI through the `$answer['content']` variable. For example:

```php
$content = $bard['content'];
echo $content;
```

## License

This package is open-source and available under the [MIT License](https://opensource.org/licenses/MIT).