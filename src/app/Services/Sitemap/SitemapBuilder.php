<?php

declare(strict_types=1);

namespace App\Services\Sitemap;

use App\Services\Sitemap\Context\AlternateLink;
use App\Services\Sitemap\Context\AlternateLinks;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;

class SitemapBuilder
{
    private DOMDocument $sitemapDom;
    private DOMElement $urlSet;

    public function __construct()
    {
        $this->sitemapDom = new DOMDocument('1.0', 'utf-8');
        $this->sitemapDom->formatOutput = true;
        $this->urlSet = $this->sitemapDom->createElement('urlset');
        $this->urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->urlSet->setAttribute('xmlns:xtml', 'http://www.w3.org/1999/xhtml');
        $this->sitemapDom->appendChild($this->urlSet);
    }

    public function addUrl(
        string          $location,
        string          $changeFrequency,
        CarbonImmutable $lastModified,
        float           $priorityValue,
        AlternateLinks  $alternateLinks,
    ): self
    {
        $url = $this->sitemapDom->createElement('url');

        $loc = $this->sitemapDom->createElement('loc');
        $text = $this->sitemapDom->createTextNode(
            htmlentities($location, ENT_QUOTES)
        );

        $loc->appendChild($text);
        $url->appendChild($loc);

        foreach ($alternateLinks->getLinks() as $link) {
            /** @var AlternateLink $link */
            $xlink = $this->sitemapDom->createElement('xtml:link');
            $xlink->setAttribute('rel', 'alternate');
            $xlink->setAttribute('href', $link->url);
            $xlink->setAttribute('hreflang', $link->locale);
            $url->appendChild($xlink);
        }

        $lastMod = $this->sitemapDom->createElement('lastmod');
        $text = $this->sitemapDom->createTextNode($lastModified->format('Y-m-d'));
        $lastMod->appendChild($text);
        $url->appendChild($lastMod);

        $changeFreq = $this->sitemapDom->createElement('changefreq');
        $text = $this->sitemapDom->createTextNode($changeFrequency);
        $changeFreq->appendChild($text);
        $url->appendChild($changeFreq);

        $priority = $this->sitemapDom->createElement('priority');
        $text = $this->sitemapDom->createTextNode((string)$priorityValue);
        $priority->appendChild($text);
        $url->appendChild($priority);

        $this->urlSet->appendChild($url);

        return $this;
    }

    public function build(): string
    {
        return $this->sitemapDom->saveXML();
    }
}
