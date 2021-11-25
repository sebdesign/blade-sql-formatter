<?php

namespace Sebdesign\BladeSqlFormatter\Compilers;

use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;

class SqlCompiler extends Compiler implements CompilerInterface
{
    public function __construct(
        protected SqlFormatter $formatter,
        protected string $indent,
        Filesystem $files,
        string $cachePath
    ) {
        parent::__construct($files, $cachePath);
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path)
    {
        $contents = $this->compileString($this->files->get($path));

        $this->ensureCompiledDirectoryExists(
            $compiledPath = $this->getCompiledPath($path)
        );

        $this->files->put($compiledPath, $contents);
    }

    /**
     * Compile the given SQL contents.
     */
    public function compileString(string $value): string
    {
        return $this->formatter->format($value, $this->indent);
    }
}
