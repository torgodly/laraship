function generateBackupConfig(backupPreferences) {
    const { backupItems, backupStorage, backupRetention } = backupPreferences;
    
    return `<?php

return [
    'backup' => [
        'name' => env('APP_NAME', 'laravel-backup'),
        'source' => [
            'files' => [
                'include' => [
                    ${backupItems.includes('files') ? 'base_path(\'storage\')' : ''}
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ]
            ],
            'databases' => [
                ${backupItems.includes('db') ? '\'mysql\'' : ''}
            ]
        ],
        'destination' => [
            'filename_prefix' => 'backup-',
            'disks' => [
                '${backupStorage}'
            ]
        ],
        'cleanup' => [
            'strategy' => 'DefaultStrategy',
            'keep_all_backups_for_days' => ${backupRetention},
            'keep_daily_backups_for_days' => ${backupRetention},
            'keep_weekly_backups_for_weeks' => 4,
            'keep_monthly_backups_for_months' => 1,
            'keep_yearly_backups_for_years' => 1,
        ]
    ]
];`;
} 