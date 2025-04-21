<?php

declare(strict_types=1);

namespace Tests\Support\WPConnector;

use DateTime;
use WPAjaxConnector\WPAjaxConnectorPHP\Objects\PostsList;
use WPAjaxConnector\WPAjaxConnectorPHP\SearchQueryInterface;

class FakeQuery implements SearchQueryInterface
{
    public function searchMeta(string $field, ?string $value = null): self
    {
        return $this;
    }

    public function orderBy(string $field, string $order = 'desc'): self
    {
        return $this;
    }

    public function count(int $count = -1): self
    {
        return $this;
    }

    public function type(string $type): self
    {
        return $this;
    }

    public function page(int $page): self
    {
        return $this;
    }

    public function parent(int $postId): self
    {
        return $this;
    }

    public function startDate(DateTime $date): self
    {
        return $this;
    }

    public function endDate(DateTime $date): self
    {
        return $this;
    }

    public function onlyPublished(bool $published): self
    {
        return $this;
    }

    public function onlyTrashed(bool $trashed): self
    {
        return $this;
    }

    public function getPosts(): PostsList
    {
        return new PostsList([], false);
    }

    public function getAttachments(): PostsList
    {
        return new PostsList([], false);
    }
}
