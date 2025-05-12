<?php

declare(strict_types=1);

namespace App\Console\Commands\Cleanup;

use App\Console\Commands\AbstractApplicationCommand;
use App\Registry\SitesRegistry;
use App\Repositories\ImageRepository;
use Carbon\Carbon;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\AttachmentData;

class FindNotUsedAttachmentsCommand extends AbstractApplicationCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-not-used-attachments {--year=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all not used images on remote website';

    public function handle(SitesRegistry $sites, ImageRepository $images): int
    {
        $year = intval($this->option('year'));

        $mainSite = $sites->getMainSiteConnector();

        $perPage = 10;
        $page = 1;
        $hasNext = true;

        while ($hasNext) {
            $posts = $mainSite->query('')
                ->onlyPublished(true)
                ->orderBy('modified', 'desc')
                ->page($page)
                ->startDate(Carbon::parse($year . '-01-01'))
                ->endDate(Carbon::parse($year . '-12-31'))
                ->count($perPage)
                ->getAttachments();

            $this->processAttachments($posts->posts, $images);

            $hasNext = $posts->hasMore;

            if ($hasNext) {
                $page++;
            }
        }

        return self::SUCCESS;
    }

    private function processAttachments(array $attachments, ImageRepository $images): void
    {
        foreach ($attachments as $attachment) {
            /** @var AttachmentData $attachment */

            if (null !== $images->findImageByExternalId($attachment->attachmentId)) {
                continue;
            }

            if (str_contains($attachment->attachmentUrl, 'preview')) {
                continue;
            }

            if (str_contains($attachment->attachmentUrl, 'logo')) {
                continue;
            }

            if (str_contains($attachment->attachmentUrl, 'favicon')) {
                continue;
            }

            if (str_contains($attachment->attachmentUrl, 'cropped-favicon')) {
                continue;
            }

            $this->info($attachment->attachmentUrl . ' (' . $attachment->attachmentId . ') ');
        }
    }
}
