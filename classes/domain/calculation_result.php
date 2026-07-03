<?php

declare(strict_types=1);

namespace block_quadratic_calc\domain;

/** Calculation results of quadratic function */
class calculation_result {
    /** @var float[] */
    public array $roots = [];

    public function __construct(array $roots) {
        $this->roots = $roots;
    }
}
