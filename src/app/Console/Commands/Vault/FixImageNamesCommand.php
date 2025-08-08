<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Image;
use App\Repositories\ArticleRepository;
use App\Services\Utils\StringUtils;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FixImageNamesCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-image-names {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes image names for article';

    /**
     * Execute the console command.
     */

    public function handle(
        ArticleRepository $articles,
    ): int
    {
        //Image names in Wordpress (technically external_path) must be the same as in vault (technically in path field)
        //If Image name differs it will cause problems during translation article to another locales
        //because we convert articles to gutenberg, and then convert them back to markdown
        //In the result service will try to download copy of image with different name
        $id = intval($this->argument('id'));

        $article = $articles->findArticleByExternalId($id);
        if ($article === null) {
            throw new RuntimeException('Article not found!');
        }

        if (false === file_exists($article->path)) {
            throw new RuntimeException('Article directory does not exist!');
        }

        foreach ($article->images as $image) {
            /** @var Image $image */
            $imageUrl = StringUtils::removeImageSize($image->external_url);
            $imageFileName = basename($imageUrl);

            if (false === file_exists($article->path . '/' . $imageFileName)) {
                $this->info("Image name mismatch: " . $imageFileName . ', ' . $image->path);


                DB::transaction(function () use ($article, $image, $imageUrl, $imageFileName): void {
                    $oldName = $image->path;

                    $image->path = $imageFileName;
                    $image->save();

                    rename($article->path . '/' . $oldName, $article->path . '/' . $imageFileName);
                });
            }
        }

        return self::SUCCESS;
    }
}
