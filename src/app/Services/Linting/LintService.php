<?php

declare(strict_types=1);

namespace App\Services\Linting;

use App\Services\Console\ApplicationOutput;
use App\Services\Linting\Context\LintingResult;
use App\Services\Linting\Linters\CodeBlocksWithoutLineBreakLinter;
use App\Services\Linting\Linters\ImageInHeadingLinter;
use App\Services\Linting\Linters\ImageNameContainsSizeLinter;
use App\Services\Linting\Linters\ImageNameContainsSubFolderLinter;
use App\Services\Linting\Linters\ImagePathIsDotLinter;
use App\Services\Linting\Linters\ImageWithoutLineBreaksLinter;
use App\Services\Linting\Linters\UnderscoreWithBackslashLinter;
use App\Services\Vault\VaultPathResolver;

class LintService
{
    private array $linters = [
        CodeBlocksWithoutLineBreakLinter::class,
        ImageInHeadingLinter::class,
        ImageNameContainsSizeLinter::class,
        ImageNameContainsSubFolderLinter::class,
        ImagePathIsDotLinter::class,
        ImageWithoutLineBreaksLinter::class,
        UnderscoreWithBackslashLinter::class,
    ];

    public function __construct(
        private VaultPathResolver $pathResolver,
        private ApplicationOutput $output,
    ) {}

    public function lintArticleFromPath(string $path, string $name): bool
    {
        $path = $this->pathResolver->resolveArticlePath($path);

        $content = file_get_contents($path.'/'.$name.'.md');

        $this->output->info("Checking $path");

        $result = $this->lintArticle($content);

        if ($result->status === 'OK') {
            $this->output->info('OK');
            $this->output->info(str_repeat('=', 50));

            return false;
        }
        $this->output->error('FAIL');
        foreach ($result->errors as $error) {
            $this->output->error($error);
        }
        $this->output->info(str_repeat('=', 50));

        return true;

    }

    public function lintArticle(string $content): LintingResult
    {
        $status = 'OK';
        $errors = [];

        foreach ($this->linters as $linterName) {
            $linter = new $linterName;

            $result = $linter->check($content);

            if ($result === true) {
                $status = 'FAIL';
                $errors[] = $linter->getErrorMessage();
            }
        }

        return new LintingResult($status, $errors);
    }
}
