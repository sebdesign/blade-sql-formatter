<?php

namespace Sebdesign\BladeSql\Directives;

use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Support\Facades\Blade;

class SqlDirective
{
    public static function compileSql(string $expression): string
    {
        $expression = Blade::stripParentheses($expression);

        if (mb_strlen($expression) > 0) {
            return vsprintf('<?php echo app(%s::class)->format(%s); ?>', [
                SqlFormatter::class,
                $expression,
            ]);
        }

        return '<?php ob_start(); ?>';
    }

    public static function compileEndSql(): string
    {
        return vsprintf('<?php echo app(%s::class)->format(ob_get_clean()); ?>', [
            SqlFormatter::class,
        ]);
    }
}
