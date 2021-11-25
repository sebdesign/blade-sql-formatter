<?php

use Doctrine\SqlFormatter\SqlFormatter;

beforeEach(function () {
    view()->addNamespace('test', __DIR__);
    test()->artisan('view:clear');
});

it('renders an sql file', function () {
    $sql = 'select * from users';

    $view = test()->view('test::stubs.select-all-users');
    $formattedAndHighlighted = app(SqlFormatter::class)->format($sql);

    expect((string) $view)->toBe($formattedAndHighlighted);
});

it('includes an sql file', function () {
    $sql = 'select * from users';

    $blade = test()->blade("@include('test::stubs.select-all-users')");
    $formattedAndHighlighted = app(SqlFormatter::class)->format($sql);

    expect((string) $blade)->toBe($formattedAndHighlighted);
});
