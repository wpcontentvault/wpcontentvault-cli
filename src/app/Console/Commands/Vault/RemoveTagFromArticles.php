<?php

declare(strict_types=1);

namespace App\Console\Commands\Vault;

use App\Console\Commands\AbstractApplicationCommand;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Vault\Manifest\V2\ManifestReader;
use App\Services\Vault\Manifest\V2\ManifestUpdater;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class RemoveTagFromArticles extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-tag-from-articles {tag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes tag from articles';

    /**
     * Execute the console command.
     */
    public function handle(
        ArticleRepository $articles,
        TagRepository     $tags,
        ManifestReader    $manifestReader,
        ManifestUpdater   $manifestUpdater
    ): int
    {
        $tag = $tags->findTagBySlug($this->argument('tag'));

        if(null === $tag) {
            throw new \InvalidArgumentException('Tag not found');
        }

        $articleList = $articles->getAllArticles();

        foreach($articleList as $article) {
            /** @var Article $article */
            if(null === $article->findTagBySlug($tag->slug)) {
                continue;
            }

            $meta = $manifestReader->loadManifestFromPath($article->path, 'original');
            $tags = $meta->tags;

            foreach($tags as $key=>$item) {
               if($tag->slug === $item->slug){
                   unset($tags[$key]);
               }
            }

            $manifestUpdater->updateTags($article->path, 'original', $tags);
        }

        return self::SUCCESS;
    }
}
