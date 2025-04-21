<?php

declare(strict_types=1);

namespace App\Console\Commands\Create;

use App\Console\Commands\AbstractApplicationCommand;
use App\Services\Utils\FilesystemUtils;
use App\Services\Vault\VaultConfigWriter;
use App\Services\Vault\VaultPathResolver;
use Josantonius\LanguageCode\LanguageCode;

use function Laravel\Prompts\form;
use function Laravel\Prompts\text;

class CreateVaultCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-vault';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes a new vault';

    /**
     * Execute the console command.
     */
    public function handle(
        VaultPathResolver $pathResolver,
        VaultConfigWriter $configWriter,
    ): int {
        $root = $pathResolver->getRoot();

        $this->info("Initializing Vault in $root...");

        if (FilesystemUtils::isFolderEmpty($root) === false) {
            $this->error('The vault directory is not empty!');

            return self::FAILURE;
        }

        $locales = $this->initLocales($configWriter, $root);
        $this->initSites($configWriter, $root, $locales);
        $this->initAi($configWriter, $root);

        mkdir($root.'articles', 0777, true);

        $this->info('Vault created successfully!');

        $this->call('migrate');
        $this->call('refresh-locales');

        return self::SUCCESS;
    }

    private function initLocales(VaultConfigWriter $writer, string $path): array
    {
        $transform = function (string $codes): array {
            if (empty(trim($codes, ' '))) {
                return [];
            }
            if (str_contains($codes, ',') === false) {
                return [$codes];
            }

            return explode(',', $codes);
        };

        $codes = text(
            label: 'Enter locale codes separated by comma, which you want to support',
            placeholder: 'en',
            default: 'en',
            required: true,
            validate: function (string $codes) use ($transform) {
                $list = $transform($codes);

                if (count($list) === 0) {
                    return 'You must specify at least one locale code for original articles.';
                }

                if (preg_match('/^[a-zA-Z, ]*$/', $codes) === 0) {
                    return 'You can enter only letters, space and commas';
                }

                return null;
            },
        );

        $locales = $transform($codes);
        $data = [];

        foreach ($locales as $item) {
            $item = trim(rtrim($item, ' '));

            if (empty($item)) {
                continue;
            }

            $data[] = [
                'code' => $item,
                'name' => LanguageCode::getName($item),
            ];
        }

        $writer->writeToPath($path, 'locales.json', $data);

        $this->info('Locales config created successfully!');

        return $locales;
    }

    private function initSites(VaultConfigWriter $writer, string $path, array $locales): void
    {
        $mainSite = form()
            ->text(label: 'Enter main site domain', name: 'domain')
            ->text(label: 'Enter access key for main site', name: 'access_key')
            ->select(label: 'Select locale for main site', options: $locales, name: 'locale')
            ->submit();

        $sites = [
            'main' => [
                'locale' => $mainSite['locale'],
                'domain' => $mainSite['domain'],
                'access_key' => $mainSite['access_key'],
            ],
            'locales' => [],
        ];

        foreach ($locales as $locale) {
            if ($locale === $mainSite['locale']) {
                continue;
            }

            $subSite = form()
                ->text(label: "Enter $locale site domain", name: 'domain')
                ->text(label: "Enter $locale site access key", name: 'access_key')
                ->submit();

            $sites['locales'][] = [
                'locale' => $locale,
                'domain' => $subSite['domain'],
                'access_key' => $subSite['access_key'],
            ];
        }

        $writer->writeToPath($path, 'sites.json', $sites);

        $this->info('Sites config created successfully!');
    }

    private function initAi(VaultConfigWriter $writer, string $path): void
    {
        $accessToken = text(
            label: 'Enter access token for Open-Router AI provider',
            validate: function (string $token) {
                if (empty($token)) {
                    return 'Access token cannot be empty!';
                }

                return null;
            });

        $ai = [
            'providers' => [
                'open_router' => [
                    'access_token' => $accessToken,
                ],
            ],
            'settings' => [
                'translation' => [
                    'provider' => 'open_router',
                    'model' => 'clause_sonnet_3_5',
                ],
                'summarize' => [
                    'provider' => 'open_router',
                    'model' => 'clause_sonnet_3_5',
                ],
            ],
        ];

        $this->info('AI config created successfully!');

        $writer->writeToPath($path, 'ai.json', $ai);
    }
}
