<?php

use Doctrine\SqlFormatter\Highlighter;
use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;

it('renders the formatted and highlighted sql', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";

    $blade = test()->blade(vsprintf('<x-sql>%s</x-sql>', ['sql' => $sql]));
    $formattedAndHighlighted = app(SqlFormatter::class)->format($sql);

    expect((string) $blade)->toBe($formattedAndHighlighted);
});

it('renders the formatted sql without highlighting', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";

    $blade = test()->blade('<x-sql :highlight="false">{!! $sql !!}</x-sql>', ['sql' => $sql]);
    $formatted = (new SqlFormatter(new NullHighlighter()))->format($sql);

    expect((string) $blade)->toBe($formatted);
});

it('renders the highlighted sql without formatting', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";

    $blade = test()->blade(sprintf('<x-sql :format="false">%s</x-sql>', $sql));
    $highlighted = (new SqlFormatter(app(Highlighter::class)))->highlight($sql);

    expect((string) $blade)->toBe($highlighted);
});

it('renders the sql without formatting and highlighting', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";

    $blade = test()->blade(sprintf('<x-sql :format="false" :highlight="false">%s</x-sql>', $sql));
    $plain = (new SqlFormatter(new NullHighlighter()))->highlight($sql);

    expect((string) $blade)->toBe($plain);
});

it('renders the formatted sql with indentation', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";
    config(['blade-sql.indent_string' => '    ']);

    $blade = test()->blade(sprintf('<x-sql>%s</x-sql>', $sql));
    $indented = (new SqlFormatter(app(Highlighter::class)))->format($sql, '    ');

    expect((string) $blade)->toBe($indented);
});

it('renders the html attributes', function () {
    $sql = "select * from `users` where `email` = 'info@example.com'";

    $blade = test()->blade(sprintf('<x-sql class="mb-0" id="sql">%s</x-sql>', $sql));
    $htmlAttributes = array_replace(config('blade-sql.html_attributes'), ['pre' => 'class="mb-0" id="sql"']);
    $rendered = (new SqlFormatter(new HtmlHighlighter($htmlAttributes)))->format($sql);

    expect((string) $blade)->toBe($rendered);
});
