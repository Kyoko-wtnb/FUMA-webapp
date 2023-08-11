<?php

return [
    0 => [
        'short_name' => 'ERROR:0',
        'long_name' => 'Error code 0',
        'description' => 'input.gwas file is missing',
        'email_message' => 'Input summary statistics file is missing, internal server error.',
        'type' => 'err',
    ],
    
    1 => [
        'short_name' => 'ERROR:100',
        'long_name' => 'Error code 100',
        'description' => 'params.config file is missing',
        'email_message' => 'Job parameters input file is missing, internal server error.',
        'type' => 'err',
    ],

    2 => [
        'short_name' => 'ERROR:001',
        'long_name' => 'Error code 001',
        'description' => 'gwas_file.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    3 => [
        'short_name' => 'ERROR:002',
        'long_name' => 'Error code 002',
        'description' => 'allSNPs.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    4 => [
        'short_name' => 'ERROR:magma',
        'long_name' => 'Error code magma',
        'description' => 'magma.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    5 => [
        'short_name' => 'ERROR:003',
        'long_name' => 'Error code 003',
        'description' => 'manhattan_filt.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    6 => [
        'short_name' => 'ERROR:004',
        'long_name' => 'Error code 004',
        'description' => 'QQSNPs_filt.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    7 => [
        'short_name' => 'ERROR:005',
        'long_name' => 'Error code 005',
        'description' => 'NoCandidates',
        'email_message' => '',
        'type' => 'err',
    ],

    8 => [
        'short_name' => 'ERROR:006',
        'long_name' => 'Error code 006',
        'description' => 'Candidates found',
        'email_message' => '',
        'type' => 'err',
    ],

    9 => [
        'short_name' => 'ERROR:007',
        'long_name' => 'Error code 007',
        'description' => 'SNPannot.R failed',
        'email_message' => '',
        'type' => 'err',
    ],

    10 => [
        'short_name' => 'ERROR:008',
        'long_name' => 'Error code 008',
        'description' => 'getGWAScatalog.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    11 => [
        'short_name' => 'ERROR:009',
        'long_name' => 'Error code 009',
        'description' => 'geteQTL.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    12 => [
        'short_name' => 'ERROR:010',
        'long_name' => 'Error code 010',
        'description' => 'getCI.R failed',
        'email_message' => '',
        'type' => 'err',
    ],

    13 => [
        'short_name' => 'ERROR:011',
        'long_name' => 'Error code 011',
        'description' => 'geneMap.R failed',
        'email_message' => '',
        'type' => 'err',
    ],

    14 => [
        'short_name' => 'ERROR:012',
        'long_name' => 'Error code 012',
        'description' => 'createCircosPlot.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    15 => [
        'short_name' => 'OK',
        'long_name' => '',
        'description' => '',
        'email_message' => '',
        'type' => 'success',
    ],

    16 => [
        'short_name' => 'ERROR:Undefined',
        'long_name' => '',
        'description' => '',
        'email_message' => '',
        'type' => 'err',
    ],

    17 => [
        'short_name' => 'ERROR:timeout',
        'long_name' => '',
        'description' => '',
        'email_message' => '',
        'type' => 'err',
    ],

    18 => [
        'short_name' => 'ERROR:cellType',
        'long_name' => '',
        'description' => '',
        'email_message' => '',
        'type' => 'err',
    ],
];