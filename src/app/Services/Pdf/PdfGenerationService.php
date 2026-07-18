<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use App\Models\Article;
use App\Models\Locale;
use App\Services\Pdf\Observers\ImagePathObserver;
use App\Services\Vault\Manifest\ManifestNameResolver;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\VaultPathResolver;
use Illuminate\Support\Str;
use Typesetterio\Typesetter\Config;
use Typesetterio\Typesetter\Observers\BreakToPageBreak;
use Typesetterio\Typesetter\Observers\DefaultMarkdownConfiguration;
use Typesetterio\Typesetter\Observers\FirstElementInChapterCSSClass;
use Typesetterio\Typesetter\Typesetter;

class PdfGenerationService
{
    public function __construct(
        private ManifestNameResolver $manifestNameResolver,
        private VaultPathResolver $vaultPathResolver,
        private ManifestReader $manifestReader,
    ){

    }
    public function generatePdf(Article $article, Locale $locale){
       $name = $this->manifestNameResolver->resolveName($article, $locale);
       $meta = $this->manifestReader->loadManifestFromPath($article->path, $name);

        $config = new Config([
            'title' => $meta->title,
            'author' => $meta->author,
            'theme' => $this->vaultPathResolver->getPdfRoot(),
            'content' => $article->path,
            'contentFilter' => function(\SplFileInfo $file) use ($name) {
                if (str_contains($file->getFilename(), $name)) {
                    return true;
                }

                return false;
            },
            'toc-enabled' => false,
            'footer' => 'Page {PAGENO}',
            'markdown-extensions' => ['md'],
            'observers' => [
                new DefaultMarkdownConfiguration(),
                new BreakToPageBreak(),
                new ImagePathObserver($article->path),
            ],
        ]);
        $service = new Typesetter();

        $pdfContent = $service->generate($config);

        file_put_contents($article->path . '/' . $name.'.pdf', $pdfContent);
    }
}
