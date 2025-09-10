<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values for DOMPDF. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false, // Throw an Exception on warnings from dompdf

    'public_path' => public_path(), // Set to Laravel's public path

    /*
     * Dejavu Sans font is missing glyphs for converted entities, turn it off if you need to show € and £.
     */
    'convert_entities' => true,

    'options' => [
        /*
         * The location of the DOMPDF font directory
         */
        'font_dir' => storage_path('fonts'), // Advised by dompdf

        /*
         * The location of the DOMPDF font cache directory
         */
        'font_cache' => storage_path('fonts'),

        /*
         * The location of a temporary directory
         */
        'temp_dir' => sys_get_temp_dir(),

        /*
         * DOMPDF's "chroot": Restrict access to files within this directory
         */
        'chroot' => [public_path(), storage_path('app/public')], // Allow access to public and storage directories

        /*
         * Protocol whitelist for URIs
         */
        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        /*
         * Operational artifact path validation
         */
        'artifactPathValidation' => null,

        /*
         * Log file for DOMPDF errors
         */
        'log_output_file' => storage_path('logs/dompdf.log'),

        /*
         * Enable font subsetting to reduce PDF size
         */
        'enable_font_subsetting' => true,

        /*
         * PDF rendering backend
         */
        'pdf_backend' => 'CPDF',

        /*
         * Default media type
         */
        'default_media_type' => 'screen',

        /*
         * Default paper size
         */
        'default_paper_size' => 'a4',

        /*
         * Default paper orientation
         */
        'default_paper_orientation' => 'portrait',

        /*
         * Default font
         */
        'default_font' => 'Arial',

        /*
         * Image DPI setting
         */
        'dpi' => 96,

        /*
         * Enable embedded PHP (use with caution)
         */
        'enable_php' => false,

        /*
         * Enable inline JavaScript for PDFs
         */
        'enable_javascript' => false,

        /*
         * Enable remote file access for images and CSS
         */
        'enable_remote' => true, // Enable remote images

        /*
         * List of allowed remote hosts
         */
        'allowed_remote_hosts' => ['bincone.apexjets.org'], // Restrict to your domain

        /*
         * Font height ratio for line height adjustment
         */
        'font_height_ratio' => 1.1,

        /*
         * Use HTML5 parser (always enabled in dompdf 2.x)
         */
        'enable_html5_parser' => true,
    ],
];
