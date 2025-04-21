<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Locale;
use WPAjaxConnector\WPAjaxConnectorPHP\WPConnectorInterface;

class ArticleExported extends ArticleEvent
{
    public function __construct(
        int $externalId,
        string $path,
        public readonly WPConnectorInterface $wpConnector,
        public readonly Locale $locale
    ) {
        parent::__construct($externalId, $path);
    }
}
