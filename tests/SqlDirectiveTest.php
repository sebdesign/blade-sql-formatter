<?php

use Doctrine\SqlFormatter\SqlFormatter;

it('renders the directive with the sql parameter', function () {
    $sql = 'select * from users';

    $blade = test()->blade('@sql($sql)', ['sql' => $sql]);
    $rendered = app(SqlFormatter::class)->format($sql);

    expect((string) $blade)->toBe($rendered);
});

it('renders the sql between the directives', function () {
    $sql = 'select * from users';

    $blade = test()->blade('@sql {{ $sql }} @endsql', ['sql' => $sql]);
    $rendered = app(SqlFormatter::class)->format($sql);

    expect((string) $blade)->toBe($rendered);
});
