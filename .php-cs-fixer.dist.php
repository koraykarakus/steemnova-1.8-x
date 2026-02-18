<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__,
        __DIR__ . '/includes', // path to subfolders if required.
    ])
    ->name('*.php')
    ->notPath('vendor');

return (new PhpCsFixer\Config())
    ->setRules([
        // base rules
        '@PSR12' => true,

        // brace position
        'braces_position' => [
            'control_structures_opening_brace'  => 'next_line_unless_newline_at_signature_end',
            'functions_opening_brace'           => 'next_line_unless_newline_at_signature_end',
            'classes_opening_brace'             => 'next_line_unless_newline_at_signature_end',
            'anonymous_functions_opening_brace' => 'next_line_unless_newline_at_signature_end',
            'anonymous_classes_opening_brace'   => 'next_line_unless_newline_at_signature_end',
        ],

        // force next line for else, catch
        'control_structure_continuation_position' => [
            'position' => 'next_line',
        ],

        // force bracers for controllers.
        'control_structure_braces' => true,
        'statement_indentation'    => true,

        // spaces between operators
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],

        // clean up extra blank lines
        'no_extra_blank_lines' => [
            'tokens' => ['extra'],
        ],

        // force new line for logic operators e.g && ||
        'operator_linebreak' => [
            'only_booleans' => true,
            'position'      => 'beginning',
        ],

        // standard spacing for if ()
        'no_extra_blank_lines' => [
            'tokens' => ['extra'],
        ],

        // indend
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],

        'statement_indentation' => true,

        // multiline indentation
        'method_argument_space' => [
            'on_multiline'                     => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],

        // short format for arrays
        'array_syntax' => ['syntax' => 'short'],

        // spacing for arrays
        'binary_operator_spaces' => [
            'default'   => 'single_space',
            'operators' => [
                '=>' => 'align_single_space_minimal',
            ],
        ],

        // comas of arrays
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],

        // indentation of arrays
        'array_indentation' => true,

        // whitespace after comma ( for array )
        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder);

// make settings below on VSCode after getting extention CS Fixer
// file > preferences > settings > open settings JSON file
/*

{
 "[php]": {
    "editor.defaultFormatter": "junstyle.php-cs-fixer"
},

// path to bat file inside vendor
"php-cs-fixer.executablePath": "C:\\xampp\\htdocs\\steemnova\\steemnova-1.8-x\\vendor\\bin\\php-cs-fixer.bat",

// path to config file cs-fixer
"php-cs-fixer.config": "C:\\xampp\\htdocs\\steemnova\\steemnova-1.8-x\\.php-cs-fixer.dist.php",
"php-cs-fixer.allowRisky": true,
"php-cs-fixer.onsave": true
}

VSCode short cut = Shift + ALT + F
*/
