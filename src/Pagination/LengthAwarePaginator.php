<?php

namespace Shopen\Pagination;

use Illuminate\Support\Collection;

class LengthAwarePaginator extends \Illuminate\Pagination\LengthAwarePaginator
{
    public function linkCollection()
    {
        return (new Collection($this->elements()))->flatMap(function ($item) {
            if (! is_array($item)) {
                return [['url' => null, 'label' => '...', 'active' => false]];
            }

            return (new Collection($item))->map(function ($url, $page) {
                return [
                    'url' => $url,
                    'label' => (string) $page,
                    'active' => $this->currentPage() === $page,
                ];
            });
        })->prepend([
            'url' => $this->previousPageUrl(),
            'label' => function_exists('__') ? __('pagination.previous') : 'Previous',
            'active' => false,
            'previous' => true,
        ])->push([
            'url' => $this->nextPageUrl(),
            'label' => function_exists('__') ? __('pagination.next') : 'Next',
            'active' => false,
            'next' => true,
        ]);
    }
}