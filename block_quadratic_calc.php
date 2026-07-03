<?php

declare(strict_types=1);

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/blocks/quadratic_calc/lib.php');

use block_quadratic_calc\adapters\moodle_calculations_repository;
use block_quadratic_calc\app\calculations_service;
use block_quadratic_calc\domain\calculation_record;
use block_quadratic_calc\domain\exceptions\invalid_coefficient_exception;
use block_quadratic_calc\domain\exceptions\validation_exception;
use block_quadratic_calc\form\calc_form;

class block_quadratic_calc extends block_base {
    private readonly calculations_service $service;

    /**
     * Initialises the block.
     */
    public function init(): void {
        $this->title = get_string('pluginname', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME);
    }

    /**
     * Gets the block contents.
     *
     * @return string The block HTML.
     */
    #[Override]
    public function get_content() {
        global $OUTPUT;
        global $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->service = new calculations_service(new moodle_calculations_repository($DB));

        $form = new calc_form($this->page->url);

        $result = null;
        $errormessage = null;
        if ($formdata = $form->get_data()) {
            try {
                $result = $this->handle_form($formdata);
            } catch (Throwable $e) {
                $errormessage = $e->getMessage();
            }
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        $roots = $result?->result->roots ?? [];

        $data = [
            'coef_a' => $result?->a,
            'coef_b' => $result?->b,
            'coef_c' => $result?->c,
            'form' => $form->render(),
            'hasroots' => !empty($roots),
            'rootsstring' => implode(', ', $roots),
            'rootsset' => $result !== null,
            'error' => $errormessage,
            'historylink' => (new moodle_url('/blocks/quadratic_calc/history.php'))->out(false),
        ];

        $this->content->text = $OUTPUT->render_from_template(BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME . '/content', $data);

        return $this->content;
    }

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => true,
            'my' => true,
        ];
    }

    private function handle_form(stdClass $formdata): calculation_record {
        global $USER;

        try {
            $result = $this->service->calculate_and_save(
                (int) $USER->id,
                (float) $formdata->a,
                (float) $formdata->b,
                (float) $formdata->c
            );
            return $result;
        } catch (validation_exception $e) {
            throw new moodle_exception('exc_service_validation', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME, '');
        } catch (invalid_coefficient_exception $e) {
            throw new moodle_exception('exc_service_invalid_coefficient', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME, '');
        } catch (Throwable $e) {
            debugging('Unknown error in block_quadratic_calc: ' . $e->getMessage(), DEBUG_DEVELOPER);
            throw new moodle_exception('exc_unknown', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME, '', null, $e->getMessage());
        }
    }
}
