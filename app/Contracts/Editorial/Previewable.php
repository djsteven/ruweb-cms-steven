<?php

namespace App\Contracts\Editorial;

interface Previewable
{
    public function previewView(): string;

    public function previewData(): array;
}
