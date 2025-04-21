<?php

declare(strict_types=1);

namespace App\Configuration;

class GlobalConfiguration
{
    public bool $replaceImages = false;

    public bool $updateImages = false;

    public bool $throwOnImageUpload = false;

    public bool $dryRun = false;

    public function replaceImages(bool $flag = true): self
    {
        $this->replaceImages = $flag;

        return $this;
    }

    public function updateImages(bool $flag = true): self
    {
        $this->updateImages = $flag;

        return $this;
    }

    public function throwOnImageUpload(bool $flag = true): self
    {
        $this->throwOnImageUpload = $flag;

        return $this;
    }

    public function shouldReplaceImages(): bool
    {
        return $this->replaceImages;
    }

    public function shouldUpdateImages(): bool
    {
        return $this->updateImages;
    }

    public function shouldThrowOnImageUpload(): bool
    {
        return $this->throwOnImageUpload;
    }
}
