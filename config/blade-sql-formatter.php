<?php

use Doctrine\SqlFormatter\HtmlHighlighter;

return [
    /*
     * The class that will be used to highlight the queries.
     * If you don't want syntax highlighting, use the `\Doctrine\SqlFormatter\NullHighlighter::class`.
     * You can also create your own class that implements the `\Doctrine\SqlFormatter\Highlighter` interface.
     */
    'highlighter' => HtmlHighlighter::class,

    /*
     * The HTML attributes that will be applied to each token using the HTML syntax highlighter.
     * Feel free to use inline styles or CSS classes to customize the syntax theme.
     */
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

    /*
     * This flag tells us if queries need to be enclosed in <pre> tags.
     */
    'use_pre' => true,

    /*
     * The indentation that will be used to format the queries.
     * If you prefer tabs, use "\t".
     */
    'indent_string' => '  ',
];
