<?php

declare(strict_types=1);

namespace App\Console\Commands\Pull;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\ArticleRepository;
use App\Services\Importing\ImageDownloader;

class PullArticlePreviewCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-article-previews {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull article preview from WordPress using its external id';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        ImageDownloader   $imageDownloader,
        SitesRegistry     $sitesConfig
    ): int
    {
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);

        if ($article === null) {
            $this->error("Article with id {$id} not found");

            return self::FAILURE;
        }

        $connector = $sitesConfig->getMainSiteConnector();
        $thumbnail = $connector->getPostThumbnail($article->external_id);

        $imageDownloader->downloadPreview($thumbnail->attachmentUrl, $article->path . 'cover/', 'original');

        if(file_exists($article->path . 'cover/original.jpg')) {
            unlink($article->path . 'cover/original.jpg');
        }

        if(file_exists($article->path . 'cover/original.jpeg')) {
            unlink($article->path . 'cover/original.jpeg');
        }

        return self::SUCCESS;
    }
}
