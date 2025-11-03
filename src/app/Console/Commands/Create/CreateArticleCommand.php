<?php

declare(strict_types=1);

namespace App\Console\Commands\Create;

use App\Console\Commands\AbstractApplicationCommand;
use App\Context\Markdown\PostMeta;
use App\Events\ArticleCreated;
use App\Events\Vault\VaultCreated;
use App\Repositories\CategoryRepository;
use App\Repositories\LocaleRepository;
use App\Services\Vault\Manifest\V2\ManifestWriter;
use App\Services\Vault\VaultPathResolver;

use Illuminate\Events\Dispatcher;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateArticleCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes a new article';

    public function handle(
        VaultPathResolver  $pathResolver,
        ManifestWriter     $manifestWriter,
        LocaleRepository   $locales,
        CategoryRepository $categories,
        Dispatcher         $dispatcher,
    ): int
    {
        $root = $pathResolver->getArticlesRoot();

        $title = text(
            label: 'Enter article title',
            validate: function (string $token) {
                if (empty($token)) {
                    return 'Title cannot be empty!';
                }

                return null;
            });
        $originalLocaleCode = select(
            label: 'Select original locale',
            options: $locales->getAllLocales()->pluck('code')->toArray()
        );

        $availableLocales = $locales->getAllLocales()
            ->pluck('code')
            ->filter(function (string $code) use ($originalLocaleCode) {
                return $code !== $originalLocaleCode;
            })
            ->toArray();
        $additionalLocaleCodes = multiselect(
            label: 'Select additional locales', options: array_values($availableLocales)
        );

        $availableCategories = $categories->getAllCategories()
            ->pluck('slug')
            ->toArray();
        $categorySlug = select(
            label: 'Select category', options: array_values($availableCategories)
        );

        $originalLocale = $locales->findLocaleByCode($originalLocaleCode);
        $category = $categories->findCategoryBySlug($categorySlug);

        $meta = new PostMeta(
            locale: $originalLocale,
            title: $title,
            status: 'draft',
            author: null,
            publishedAt: null,
            modifiedAt: null,
            url: null,
            externalId: null,
            category: $category,
            tags: []
        );

        $articlePath = $root . $title . '/';

        if (file_exists($articlePath)) {
            $this->error('Article already exists!');

            return self::FAILURE;
        }

        mkdir($articlePath, 0755, true);
        mkdir($articlePath . 'cover', 0755, true);
        touch($articlePath . 'original.md');

        $manifestWriter->writeManifest($articlePath, 'original', $meta);

        foreach ($additionalLocaleCodes as $code) {
            $locale = $locales->findLocaleByCode($code);

            $meta = new PostMeta(
                locale: $locale,
                title: '',
                status: 'draft',
                author: null,
                publishedAt: null,
                modifiedAt: null,
                url: null,
                externalId: null,
                category: null,
                tags: []
            );

            $manifestWriter->writeManifest($articlePath, $locale->code, $meta);
        }

        $this->info("Article $articlePath created.");

        $dispatcher->dispatch(new ArticleCreated(
            externalId: 0,
            path: $articlePath
        ));

        return self::SUCCESS;
    }
}
