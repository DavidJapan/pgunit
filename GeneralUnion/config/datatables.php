<?php

//The name of this file should match the name of the base directory of the application.
//The application name in the .env file might be different from the directory name.
return [
    'ROLE_ADMINISTRATOR' => env('ROLE_ADMINISTRATOR', 'Administrator'),
    'ROLE_REPORT_WRITER' => env('ROLE_REPORT_WRITER', 'Report Writer'),
    'ROLE_REPORT_EDITOR' => env('ROLE_REPORT_EDITOR', 'Report Editor'),
    'ROLE_COLLECTIVE_AGREEMENT_EDITOR' => env('ROLE_COLLECTIVE_AGREEMENT_EDITOR', 'Collective Agreement Editor'),
    'ROLE_USER' => env('ROLE_USER', 'User'),
    'ROLE_DEBUG' => env('ROLE_DEBUG', 'Debug'),
    'PATH_TO_EXCEL' => env('PATH_TO_EXCEL'),
    'PATH_TO_PDF' => env('PATH_TO_PDF'),
    'PATH_TO_DB_BACKUPS' => env('PATH_TO_DB_BACKUPS'), 
    'DB_PASSWORD' => env('DB_PASSWORD'),
    'DB_DATABASE' => env('DB_DATABASE'),
    'SCHEMA' => env('SCHEMA', 'gu'),
    'models' => [
        'qunit' => 'QUnitModel',
        'administer_users' => 'AdministerUser',
        'administer_roles' => 'AdministerRole',
        'sectors' => 'Sector',
        'branches' => 'Branch',
        'individual_reports' => 'IndividualReport',
        'reports' => 'Report',
        'employers' => 'Employer',
        'report_headings' => 'ReportHeading',
        'collective_agreements' => 'CollectiveAgreement',
        'uploaded_files' => 'UploadedFile',
        'db_backup_files' => 'DBBackupFile',
    ]
];
