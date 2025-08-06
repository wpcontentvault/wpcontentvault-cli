<?php

declare(strict_types=1);

namespace App\Services\Exporting;

use App\Events\ArticleExported;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use App\Services\Console\ApplicationOutput;
use App\Services\Exporting\Checksum\ChecksumCalculator;
use App\Services\Exporting\Mapper\ImageMapper;
use App\Services\Vault\Manifest\V1\ManifestReader;
use App\Services\Vault\Manifest\V1\ManifestUpdater;
use App\Services\Vault\MarkdownLoader;
use App\Services\Vault\Meta\ExportChecksumMeta;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use RuntimeException;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ArticleExporter
{
    public function __construct(
        private ArticleRepository  $articles,
        private GutenbergRenderer  $renderer,
        private ManifestReader     $manifestReader,
        private ManifestUpdater    $manifestUpdater,
        private SitesRegistry      $sitesConfiguration,
        private MarkdownLoader     $markdownLoader,
        private ImageMapper        $imageMapper,
        private ApplicationOutput  $output,
        private ChecksumCalculator $checksumCalculator,
        private ExportChecksumMeta $exportChecksumMeta,
        private Dispatcher         $eventDispatcher,
    ) {}

    public function exportLocalizationFromDir(string $path, string $name, bool $dryRun = false): void
    {
        $meta = $this->manifestReader->loadManifestFromPath($path, $name);
        $blocks = $this->markdownLoader->loadBlocksFromPath($path, $name);

        if ($meta->serializedId === null) {
            throw new RuntimeException('Main article is not serialized yet!');
        }

        $article = $this->articles->findArticleByUuid($meta->serializedId);

        if ($article === null) {
            throw new RuntimeException('Main article not found in DB!');
        }

        $this->imageMapper->mapImagesToBlocks($blocks, $article);

        if (false === $this->sitesConfiguration->hasSiteConnectorForLocale($meta->locale)) {
            $this->output->warning("No connector configured for {$meta->locale->code}");

            return;
        }

        $connector = $this->sitesConfiguration->getSiteConnectorByLocale($meta->locale);

        if ($meta->externalId === null) {
            $postInfo = $connector->addPost($meta->title, '');
            $this->manifestUpdater->updateExternalIdAndUrl(
                $path,
                $name,
                $postInfo->id,
                $postInfo->url
            );
            $externalId = $postInfo->id;
        } else {
            $externalId = $meta->externalId;
        }

        $this->exportArticle($path, $blocks, $externalId, $connector, $name);

        $this->eventDispatcher->dispatch(new ArticleExported(
                intval($article->external_id),
                $path,
                $connector,
                $meta->locale
            )
        );
    }

    public function exportArticle(
        string               $path,
        Collection           $blocks,
        int                  $externalId,
        WPConnectorInterface $connector,
        string               $name,
    ): void
    {
        $rendered = $this->renderer->render($blocks);

        $sum = $this->checksumCalculator->calculateChecksumForBlocks($rendered);
        $savedSum = $this->exportChecksumMeta->readExportChecksum($path, $name);

        if ($this->isArticleChanged($externalId, $sum, $savedSum, $connector)) {
            $connector->setPostBlocks($externalId, $rendered);

            $this->exportChecksumMeta->writeExportChecksum($path, $name, $sum);

            $this->output->info("Article {$externalId} updated.");
        } else {
            $this->output->info("Article $externalId not changed, skipping.");
        }

    }

    private function isArticleChanged(int $externalId, string $sum, ?string $savedSum, WPConnectorInterface $connector): bool
    {
        $postBlocks = $connector->getPostBlocks($externalId);

        $oldSum = $this->checksumCalculator->calculateChecksumForBlocks($postBlocks);

        if (empty($savedSum) === false && $savedSum !== $oldSum) {
            $this->output->info('Remote checksum is different from saved!');
        }

        if ($oldSum !== $sum) {
            return true;
        }

        return false;

    }
}
