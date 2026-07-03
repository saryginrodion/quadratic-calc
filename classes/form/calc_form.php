<?php

declare(strict_types=1);

namespace block_quadratic_calc\form;

use Override;

class calc_form extends \moodleform {
    #[Override]
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'a', get_string('input_a_coef', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));
        $mform->setType('a', PARAM_RAW_TRIMMED);
        $mform->addRule('a', get_string('exc_validation_not_numeric', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME), 'numeric', null, 'client');

        $mform->addElement('text', 'b', get_string('input_b_coef', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));
        $mform->setType('b', PARAM_RAW_TRIMMED);
        $mform->addRule('b', get_string('exc_validation_not_numeric', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME), 'numeric', null, 'client');

        $mform->addElement('text', 'c', get_string('input_c_coef', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));
        $mform->setType('c', PARAM_RAW_TRIMMED);
        $mform->addRule('c', get_string('exc_validation_not_numeric', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME), 'numeric', null, 'client');

        $this->add_action_buttons(false, get_string('button_find_roots', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME));
    }

    #[Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        foreach (['a', 'b', 'c'] as $name) {
            if (!is_numeric($data[$name])) {
                $errors[$name] = get_string('exc_validation_not_numeric', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME);
            }
        }

        if (is_numeric($data['a']) && (float) $data['a'] == 0.0) {
            $errors['a'] = get_string('exc_validation_coef_a_invalid', BLOCK_QUADRATIC_CALC_FRANKENSTYLE_NAME);
        }

        return $errors;
    }
}
