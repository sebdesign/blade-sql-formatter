# Display formatted SQL queries in your Laravel views

[![GitHub license](https://img.shields.io/github/license/sebdesign/blade-sql)](https://github.com/sebdesign/blade-sql/blob/main/LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/sebdesign/blade-sql.svg)](https://packagist.org/packages/sebdesign/blade-sql)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/sebdesign/blade-sql/run-tests?label=tests)](https://github.com/sebdesign/blade-sql/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/sebdesign/blade-sql/Check%20&%20fix%20styling?label=code%20style)](https://github.com/sebdesign/blade-sql/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sebdesign/blade-sql.svg)](https://packagist.org/packages/sebdesign/blade-sql)

A small Laravel package for formatting SQL statements and files inside your Blade templates.

This package uses [`doctrine/sql-formatter`](https://github.com/doctrine/sql-formatter) to indent and add line breaks in addition to syntax highlighting.

## Installation

> **Requires [PHP 8.0+](https://php.net/releases) and [Laravel 8+](https://laravel.com/docs/8.x/releases)**

You can install the package via composer:

```bash
composer require sebdesign/blade-sql
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="blade-sql-config"
```

This is the contents of the published config file:

```php
<?php

use Doctrine\SqlFormatter\HtmlHighlighter;

return [
    'highlighter' => HtmlHighlighter::class,

    'html_attributes' => [
        HtmlHighlighter::HIGHLIGHT_QUOTE => 'style="color: blue;"',
        HtmlHighlighter::HIGHLIGHT_BACKTICK_QUOTE => 'style="color: purple;"',
        HtmlHighlighter::HIGHLIGHT_RESERVED => 'style="font-weight:bold;"',
        HtmlHighlighter::HIGHLIGHT_BOUNDARY => '',
        HtmlHighlighter::HIGHLIGHT_NUMBER => 'style="color: green;"',
        HtmlHighlighter::HIGHLIGHT_WORD => 'style="color: #333;"',
        HtmlHighlighter::HIGHLIGHT_ERROR => 'style="background-color: red;"',
        HtmlHighlighter::HIGHLIGHT_COMMENT => 'style="color: #aaa;"',
        HtmlHighlighter::HIGHLIGHT_VARIABLE => 'style="color: orange;"',
        HtmlHighlighter::HIGHLIGHT_PRE => 'style="color: black; background-color: white;"',
    ],

    'use_pre' => true,

    'indent_string' => '  ',
];
```

Feel free to customize the style of each token. You can use inline styles or CSS classes, even Tailwind CSS. Check out the demo on [Tailwind Play](https://play.tailwindcss.com/JXXKktftlS).

## Usage

Input:
```
select * from `users` where `id` = 1 limit 1
```

Output:

<pre style="color: black; background-color: white;"><span style="font-weight:bold;">select</span>
  <span >*</span>
<span style="font-weight:bold;">from</span>
  <span style="color: purple;">`users`</span>
<span style="font-weight:bold;">where</span>
  <span style="color: purple;">`id`</span> <span >=</span> <span style="color: green;">1</span>
<span style="font-weight:bold;">limit</span>
  <span style="color: green;">1</span>
</pre>

### View component

```html
<!-- Formatting and syntax highlighting -->
<x-sql>
    select * from `users` where `id` = 1 limit 1
</x-sql>

<!-- Component attributes -->
<x-sql class="bg-gray-100 rounded-xl">
    select * from `users` where `id` = 1 limit 1
</x-sql>

<!-- Syntax highlighting only -->
<x-sql format="false">
    select * from `users` where `id` = 1 limit 1
</x-sql>

<!-- Formatting only -->
<x-sql highlight="false">
    select * from `users` where `id` = 1 limit 1
</x-sql>
```

### HTML Entity Encoding#

If you use Blade's `{{ }}` echo statements inside the `<x-sql>` component, they will be sent through `htmlspecialchars` automatically.
If your SQL statement contains single `'` or double `"` quotes, they will be double-encoded.

For example:
```html
@php($sql = "select * from `users` where `email` = 'info@example.com'")

<x-sql>{{ $sql }}</x-sql>
```

Will output:
<pre style="color: black; background-color: white;"><span style="font-weight:bold;">select</span>
  <span >*</span>
<span style="font-weight:bold;">from</span>
  <span style="color: purple;">`users`</span>
<span style="font-weight:bold;">where</span>
  <span style="color: purple;">`email`</span> <span >=</span> <span >&amp;</span> <span style="color: #aaa;">#039;info@example.com&amp;#039;</span></pre>

In order to address this, you can use raw echo statements `{!! !!}` on your own responsibility.

> Learn more about [displaying unescaped data](https://laravel.com/docs/8.x/blade#displaying-unescaped-data).

For example:
```html
@php($sql = "select * from `users` where `email` = 'info@example.com'")

<x-sql>{!! $sql !!}</x-sql>
```

Will output:
<pre style="color: black; background-color: white;"><span style="font-weight:bold;">select</span>
  <span >*</span>
<span style="font-weight:bold;">from</span>
  <span style="color: purple;">`users`</span>
<span style="font-weight:bold;">where</span>
  <span style="color: purple;">`email`</span> <span >=</span> <span style="color: blue;">'info@example.com'</span></pre>

### Blade directive

```html
<!-- Format the SQL statement string -->
@sql('select * from `users` where `id` = 1 limit 1')

<!-- Format the SQL statement block -->
@sql
    select * from `users` where `id` = 1 limit 1
@endsql
```

### Rendering and including views

You can render and include SQL files like Blade files inside your controllers and your views. The SQL files will be compiled as formatted and highlighted HTML files, and will be cached in the compiled view path, e.g.: `storage/framework/views`.

If you don't want to store you SQL files in the `resources/views` directory, you can load them from another location, by adding the path in the `paths` key of your `config/view.php` file.

The following example will use the `database/queries` directory to find SQL files:

```php
return [
    'paths' => [
        resource_path('views'),
        database_path('queries'),
    ],
];
```

If you prefer using a namespace to separate your Blade files from your SQL files, you can add one in the `boot` method of a service provider.

The following example will use the `database/queries` directory to find SQL files with the `sql::` namespace:

> In this case you don't need to add the path to `config/view.php`.

```php
public function boot()
{
    $this->loadViewsFrom(database_path('queries'), 'sql');
}
```

Render `database/queries/users/select-first-user.sql` from a controller.
```php
public function show()
{
    return view('sql::users.select-first-user');
}
```

Include `database/queries/users/select-first-user.sql` within a Blade view.
```php
@include('sql::users.select-first-user')
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Sébastien Nikolaou](https://github.com/sebdesign)
- [Grégoire Paris](https://github.com/greg0ire)
- [Jeremy Dorn](https://github.com/jdorn)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
