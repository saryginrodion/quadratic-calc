<?php

declare(strict_types=1);

namespace block_quadratic_calc\app;

use block_quadratic_calc\domain\calculation_record;
use block_quadratic_calc\domain\calculation_result;
use block_quadratic_calc\domain\exceptions\invalid_coefficient_exception;
use block_quadratic_calc\domain\exceptions\validation_exception;
use block_quadratic_calc\domain\pagination_opts;
use block_quadratic_calc\domain\pagination_result;

class calculations_service {
    public function __construct(
        private readonly calculations_repository_port $repository,
    ) {
    }

    public function calculate_and_save(
        int $userid,
        float $a,
        float $b,
        float $c,
    ): calculation_record {
        if ($a == 0) {
            throw new invalid_coefficient_exception('coefficient a can not be zero');
        }

        $result = $this->calculate($a, $b, $c);

        $record = $this->repository->create(new calculation_record(
            null,
            $userid,
            $a,
            $b,
            $c,
            $result,
            time(),
        ));

        return $record;
    }

    /**
     * @return pagination_result<calculation_record>
     */
    public function get_history(
        int $userid,
        pagination_opts $pagination,
    ): pagination_result {
        if ($pagination->limit > BLOCK_QUADRATIC_CALC_ITEMS_ON_PAGE_LIMIT) {
            throw new validation_exception('invalid limit value. must be in bounds from 0 and ' . BLOCK_QUADRATIC_CALC_ITEMS_ON_PAGE_LIMIT);
        }

        $result = $this->repository->list($userid, $pagination);
        return $result;
    }

    private static function calculate(
        float $a,
        float $b,
        float $c,
    ): calculation_result {
        $d = $b * $b - 4.0 * $a * $c;

        $roots = [];

        if ($d == 0.0) {
            $roots = [-$b / ($a * 2.0)];
        } else if ($d > 0.0) {
            $dsqrt = sqrt($d);
            $roots = [
                (-$b - $dsqrt) / ($a * 2.0),
                (-$b + $dsqrt) / ($a * 2.0),
            ];
        }

        return new calculation_result($roots);
    }
}
