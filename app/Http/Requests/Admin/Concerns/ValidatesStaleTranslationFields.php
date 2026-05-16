<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Models\Page;
use App\Services\Content\ContentSchemaRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

trait ValidatesStaleTranslationFields
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $entity = $this->staleTranslationEntity();

            if (! $entity || $entity->isBaseLocale()) {
                return;
            }

            $registry = app(ContentSchemaRegistry::class);
            $acknowledged = $this->input('acknowledged_fields', []);
            $acknowledged = is_array($acknowledged) ? $acknowledged : [];

            foreach ($entity->staleTranslatableFields() as $path) {
                $formName = $registry->formNameFor($entity, $path);

                if (in_array($formName, $acknowledged, true)) {
                    continue;
                }

                if ($this->staleFieldWasModified($entity, $path)) {
                    continue;
                }

                $validator->errors()->add($this->requestDotPath($entity, $path), __('admin.save_blocked_outdated'));
            }
        });
    }

    abstract protected function staleTranslationEntity(): ?Model;

    private function staleFieldWasModified(Model $entity, string $path): bool
    {
        $requestPath = $this->requestDotPath($entity, $path);

        return $this->normalizedValue(data_get($this->all(), $requestPath))
            !== $this->normalizedValue($this->currentEntityValue($entity, $path));
    }

    private function requestDotPath(Model $entity, string $path): string
    {
        if ($entity instanceof Page && (str_starts_with($path, 'meta.') || str_starts_with($path, 'sections.'))) {
            return 'content_json.'.$path;
        }

        return $path;
    }

    private function currentEntityValue(Model $entity, string $path): mixed
    {
        if ($entity instanceof Page && str_starts_with($path, 'meta.')) {
            return data_get($entity->content_json ?? [], $path);
        }

        if ($entity instanceof Page && str_starts_with($path, 'sections.')) {
            return data_get($entity->content_json ?? [], $path);
        }

        $segments = explode('.', $path);
        $root = array_shift($segments);
        $value = $entity->getAttribute($root);

        return $segments === [] ? $value : data_get($value, implode('.', $segments));
    }

    private function normalizedValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value)) {
            $value = $this->sortArrayValue($value);

            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        return (string) $value;
    }

    private function sortArrayValue(array $value): array
    {
        ksort($value);

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->sortArrayValue($item);
            }
        }

        return $value;
    }
}
