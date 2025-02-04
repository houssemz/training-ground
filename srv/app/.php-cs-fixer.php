<?php

$finder = \PhpCsFixer\Finder::create()->in(['src', 'config']);

return (new \PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP83Migration' => true,
        '@DoctrineAnnotation' => true,

        // @Symfony code styles rules blacklisting:
        'method_chaining_indentation' => false,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_singleline' => false,
        'php_unit_fqcn_annotation' => false,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_indent' => false,
        'phpdoc_inline_tag_normalizer' => false,
        'phpdoc_no_access' => false,
        'phpdoc_no_alias_tag' => false,
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_package' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_return_self_reference' => false,
        'phpdoc_scalar' => false,
        'phpdoc_separation' => false,
        'phpdoc_single_line_var_spacing' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_trim' => false,
        'phpdoc_types' => false,
        'phpdoc_var_without_name' => false,
        'error_suppression' => false,
        'standardize_not_equals' => false,
        'single_line_throw' => false,
        // @Symfony customised rules
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'concat_space' => ['spacing' => 'one'],
        'native_function_invocation' => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true],
        'phpdoc_types_order' => ['null_adjustment' => 'always_first'],
        'single_quote' => ['strings_containing_single_quote_chars' => true],
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'cast_spaces' => ['space' => 'single'],
        'operator_linebreak' => [
            'only_booleans' => true,
            'position' => 'beginning',
        ],

        // Additional code style rules whitelisting:
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'method_public_abstract_static',
                'method_public_abstract',
                'method_protected_abstract_static',
                'method_protected_abstract',
                'phpunit',
                'method_public_static',
                'method_public',
                'magic',
                'method_protected_static',
                'method_protected',
                'method_private_static',
                'method_private'
            ]
        ],
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'fully_qualified_strict_types' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'mb_str_functions' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_alternative_syntax' => true,
        'no_superfluous_elseif' => true,
        'ordered_imports' => true,
        'control_structure_braces' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_no_expectation_annotation' => true,
        'php_unit_namespaced' => true,
        'php_unit_expectation' => true,
        'php_unit_dedicate_assert_internal_type' => true,
        'single_blank_line_at_eof' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
;
