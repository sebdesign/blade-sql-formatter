<?php

namespace Sebdesign\BladeSqlFormatter;

use Doctrine\SqlFormatter\Highlighter;
use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\Engines\CompilerEngine;
use Sebdesign\BladeSqlFormatter\Compilers\SqlCompiler;
use Sebdesign\BladeSqlFormatter\Components\SqlComponent;
use Sebdesign\BladeSqlFormatter\Directives\SqlDirective;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeSqlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blade-sql-formatter')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->registerHighlighters();
        $this->registerSqlFormatter();
        $this->registerSqlCompiler();
    }

    protected function registerHighlighters(): void
    {
        $this->app->bind(Highlighter::class, function (Application $app, array $parameters = []) {
            /** @var class-string<Highlighter> $highlighter */
            $highlighter = config('blade-sql-formatter.highlighter') ?? HtmlHighlighter::class;

            return $app->make($highlighter, $parameters);
        });

        $this->app->bind(HtmlHighlighter::class, function (Application $app, array $parameters = []) {
            /** @var array<string,string> $htmlAttributes */
            $htmlAttributes = config('blade-sql-formatter.html_attributes', []);
            $htmlAttributes = array_replace($htmlAttributes, $parameters['htmlAttributes'] ?? []);

            /** @var bool $usePre */
            $usePre = (bool) config('blade-sql-formatter.use_pre', true);

            return new HtmlHighlighter($htmlAttributes, $usePre);
        });
    }

    protected function registerSqlFormatter(): void
    {
        $this->app->bind(SqlFormatter::class, function (Application $app) {
            /** @var Highlighter $highlighter */
            $highlighter = $app->make(Highlighter::class);

            return new SqlFormatter($highlighter);
        });
    }

    protected function registerSqlCompiler(): void
    {
        $this->app->singleton(SqlCompiler::class, function (Application $app) {
            /** @var SqlFormatter $formatter */
            $formatter = $app->make(SqlFormatter::class);

            /** @var string $indent */
            $indent = config('blade-sql-formatter.indent_string', '  ');

            /** @var Filesystem $filesystem */
            $filesystem = $app->make('files');

            /** @var string $cachePath */
            $cachePath = config('view.compiled');

            return new SqlCompiler($formatter, $indent, $filesystem, $cachePath);
        });
    }

    public function packageBooted(): void
    {
        Blade::component('sql', SqlComponent::class);
        Blade::directive('sql', [SqlDirective::class, 'compileSql']);
        Blade::directive('endsql', [SqlDirective::class, 'compileEndSql']);

        View::addExtension('sql', 'sql', function () {
            /** @var SqlCompiler $compiler */
            $compiler = $this->app->make(SqlCompiler::class);

            /** @var Filesystem $filesystem */
            $filesystem = $this->app->make('files');

            return new CompilerEngine($compiler, $filesystem);
        });
    }
}
