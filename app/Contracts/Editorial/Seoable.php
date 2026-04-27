<?php

namespace App\Contracts\Editorial;

interface Seoable
{
    public function meta(): array;

    public function url(): string;

    public function seoTitleFallback(): ?string;
}
