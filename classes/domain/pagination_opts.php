<?php

declare(strict_types=1);

namespace block_quadratic_calc\domain;

use InvalidArgumentException;

class pagination_opts {
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
    ) {
        if ($this->page < 0) {
            throw new InvalidArgumentException('page must be >= 0');
        }

        if ($this->limit < 1) {
            throw new InvalidArgumentException('limit must be >= 1');
        }
    }

    public function offset(): int {
        return $this->page * $this->limit;
    }
}
