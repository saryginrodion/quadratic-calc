<?php

declare(strict_types=1);

use block_quadratic_calc\adapters\moodle_calculations_repository;
use block_quadratic_calc\app\calculations_service;
use block_quadratic_calc\domain\pagination_opts;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/quadratic_calc/lib.php');


global $DB, $OUTPUT, $PAGE, $USER;

require_login(null, false);

$page = optional_param('page', 0, PARAM_INT);
if ($page < 0) {
    $page = 0;
}
$limit = 15;

$url = new moodle_url('/blocks/quadratic_calc/history.php', ['page' => $page]);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('title_history', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));
$PAGE->set_heading(get_string('title_history', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));

$service = new calculations_service(new moodle_calculations_repository($DB));

$result = null;
$errormessage = null;
try {
    $result = $service->get_history((int) $USER->id, new pagination_opts((int) $page, (int) $limit));
} catch (Throwable $e) {
    debugging('Unknown error in block_quadratic_calc history page: ' . $e->getMessage(), DEBUG_DEVELOPER);
    $errormessage = get_string('exc_unknown', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME);
}

$items = [];

if ($result != null) {
    foreach ($result->items as $item) {
        $items[] = [
            'a' => $item->a,
            'b' => $item->b,
            'c' => $item->c,
            'createdat' => userdate($item->createdat),
            'hasroots' => !empty($item->result->roots),
            'rootsstring' => implode(', ', $item->result->roots),
        ];
    }
}

$data = [
    'items' => $items,
    'hasitems' => !empty($items),
    'error' => $errormessage,
];

echo $OUTPUT->header();

echo $OUTPUT->render_from_template(BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME . '/history', $data);

if ($result !== null) {
    $pagingbar = new paging_bar($result->total, $result->page, $result->limit, $url);
    echo $OUTPUT->render($pagingbar);
}

echo $OUTPUT->footer();
