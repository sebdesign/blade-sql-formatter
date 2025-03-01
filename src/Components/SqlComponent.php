<?php

namespace Sebdesign\BladeSqlFormatter\Components;

use Closure;
use Doctrine\SqlFormatter\Highlighter;
use Doctrine\SqlFormatter\HtmlHighlighter;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

class SqlComponent extends Component
{
    private string $indent;

    public function __construct(
        public bool $format = true,
        public bool $highlight = true,
    ) {
        /** @phpstan-ignore-next-line */
        $this->indent = config('blade-sql-formatter.indent_string', '  ');
    }

    public function render(): Closure
    {
        return fn (array $data) => $this->format($data['slot'], $data['attributes']);
    }

    /**
     * @param ComponentAttributeBag<string,string> $attributes
     */
    private function format(Htmlable $slot, ComponentAttributeBag $attributes): string
    {
        $highlighter = $this->highlighter($attributes);
        $formatter = $this->formatter($highlighter);

        return $this->format
            ? $formatter->format($slot->toHtml(), $this->indent)
            : $formatter->highlight($slot->toHtml());
    }

    /**
     * @param ComponentAttributeBag<string,string> $attributes
     */
    private function highlighter(ComponentAttributeBag $attributes): Highlighter
    {
        return $this->highlight
            ? app(Highlighter::class, ['htmlAttributes' => $this->formatAttributes($attributes)])
            : new NullHighlighter();
    }

    /**
     * @param ComponentAttributeBag<string,string> $attributes
     * @return array<string,string>
     */
    private function formatAttributes(ComponentAttributeBag $attributes): array
    {
        return array_filter(
            [HtmlHighlighter::HIGHLIGHT_PRE => (string) $attributes],
            fn (string $attributes) => $attributes !== '',
        );
    }

    private function formatter(Highlighter $highlighter): SqlFormatter
    {
        return new SqlFormatter($highlighter);
    }
}
