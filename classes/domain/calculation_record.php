<?php

declare(strict_types=1);

namespace block_quadratic_calc\domain;

class calculation_record {
    public function __construct(
        public readonly int|null $id,
        public readonly int $userid,
        public readonly float $a,
        public readonly float $b,
        public readonly float $c,
        public readonly calculation_result $result,
        public readonly int $createdat,
    ) {
    }
}
