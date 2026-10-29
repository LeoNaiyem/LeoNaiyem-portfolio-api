<?php

namespace App\Models\Concerns;

use App\Jobs\RevalidateFrontend;

/**
 * Tells the Next.js frontend to refresh its cached pages when content changes.
 */
trait RevalidatesFrontend
{
    public static function bootRevalidatesFrontend(): void
    {
        $revalidate = function ($model) {
            foreach ($model->revalidationTags() as $tag) {
                RevalidateFrontend::dispatch($tag)->afterCommit();
            }
        };

        static::saved($revalidate);
        static::deleted($revalidate);
    }

    /**
     * @return list<string>
     */
    public function revalidationTags(): array
    {
        return [$this->getTable()];
    }
}
