<?php

declare(strict_types=1);

namespace block_quadratic_calc\domain;

/**
 * @template T pagination items type
 */
class pagination_result {
    /** @param T $items */
    public function __construct(
        public int $page,
        public int $limit,
        /** @var array<T> */
        public array $items,
        public int $total,
    ) {
    }
}
