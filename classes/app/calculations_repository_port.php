<?php

declare(strict_types=1);

namespace block_quadratic_calc\app;

use block_quadratic_calc\domain\calculation_record;
use block_quadratic_calc\domain\pagination_opts;
use block_quadratic_calc\domain\pagination_result;

interface calculations_repository_port {
    /** Creates new calculation_record. ignores id
     * @return calculation_record the same record with generated id */
    function create(calculation_record $record): calculation_record;
    /** @return pagination_result<calculation_record> */
    function list(int $userid, pagination_opts $pagination): pagination_result;
}
