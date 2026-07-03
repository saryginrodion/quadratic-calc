<?php

declare(strict_types=1);

namespace block_quadratic_calc\adapters;

use block_quadratic_calc\app\calculations_repository_port;
use block_quadratic_calc\domain\calculation_record;
use block_quadratic_calc\domain\calculation_result;
use block_quadratic_calc\domain\pagination_opts;
use block_quadratic_calc\domain\pagination_result;
use moodle_database;
use Override;
use stdClass;
use UnexpectedValueException;

class moodle_calculations_repository implements calculations_repository_port {
    private const TABLE = 'block_quadratic_calc_history';

    public function __construct(
        private readonly moodle_database $db,
    ) {
    }

    #[Override]
    public function create(calculation_record $record): calculation_record {
        $data = $this->record_to_stdclass($record);
        $id = $this->db->insert_record(self::TABLE, $data, true, false);
        return new calculation_record(
            $id,
            $record->userid,
            $record->a,
            $record->b,
            $record->c,
            $record->result,
            $record->createdat,
        );
    }

    #[Override]
    public function list(int $userid, pagination_opts $pagination): pagination_result {
        $conditions = ['userid' => $userid];

        $items = $this->db->get_records(
            self::TABLE,
            $conditions,
            'createdat DESC',
            '*',
            $pagination->offset(),
            $pagination->limit
        );

        $total = $this->db->count_records(self::TABLE, $conditions);

        $records = [];
        foreach ($items as $item) {
            $records[] = $this->record_from_stdclass($item);
        }

        return new pagination_result(
            $pagination->page,
            $pagination->limit,
            $records,
            $total,
        );
    }

    private function record_to_stdclass(calculation_record $record): stdClass {
        $result = new stdClass();

        if ($record->id !== null) {
            $result->id = $record->id;
        }
        $result->userid = $record->userid;
        $result->a = $record->a;
        $result->b = $record->b;
        $result->c = $record->c;
        $result->roots = json_encode($record->result->roots);
        $result->createdat = $record->createdat;

        return $result;
    }

    private function record_from_stdclass(stdClass $value): calculation_record {
        $roots = json_decode($value->roots);
        if (!is_array($roots)) {
            throw new UnexpectedValueException('roots from DB is not an array. check roots data for corruption');
        }

        return new calculation_record(
            (int) $value->id,
            (int) $value->userid,
            (float) $value->a,
            (float) $value->b,
            (float) $value->c,
            new calculation_result($roots),
            (int) $value->createdat,
        );
    }
}
