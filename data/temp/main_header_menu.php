<?php
return [
    [
        'id'=>'file', 'label'=>'文件', 'icon'=>'fa-cubes',
        'childs'=>[
            [
                'label'=>'Dashboards', 
                'icon'=>'fa-dashboard', 
                'childs'=>[
                    ['label'=>'Welcome', 'icon'=>'fa-star'],
                    ['label'=>'Add Dashboard', 'icon'=>'fa-plus'],
                ]
            ],
            ['label'=>'Open Document', 'icon'=>'fa-file-text'],
            ['label'=>'Open Asset', 'icon'=>'fa-picture-o'],
            ['label'=>'Search & Replace Assignments', 'icon'=>'fa-object-group'],
            ['label'=>'Recently Opened Elements', 'icon'=>'fa-clock-o'],
            ['label'=>'Seemode', 'icon'=>'fa-eye'],
            ['label'=>'Close all tabs', 'icon'=>'fa-times'],
            ['label'=>'Help', 'icon'=>'fa-handshake-o'],
            ['label'=>'ABOUT PIMCORE®', 'icon'=>'fa-info'],
        ]
    ],
    
    [
        'id'=>'Tools', 'label'=>'工具', 'icon'=>'fa-wrench',
        'childs'=>[
            ['label'=>'Glossary'],
            ['label'=>'Redirects'],
            ['label'=>'Translation'],
            ['label'=>'Recycle Bin'],
            ['label'=>'Extensions'],
            ['label'=>'Notes & Events'],
            ['label'=>'Application Logger'],
            ['label'=>'Backup'],
            ['label'=>'Email'],
            ['label'=>'Update'],
            ['label'=>'Maintenance Mode'],
            ['label'=>'System Info & Tools'],
        ]
    ],

    
    [
        'id'=>'marketing', 'label'=>'行銷', 'icon'=>'fa-line-chart',
        'childs'=>[
            ['label'=>'Reports'],
            ['label'=>'Tag & Snippet Management'],
            ['label'=>'QR-Codes'],
            ['label'=>'Personalization / Targeting'],
            ['label'=>'Search Engine Optimization'],
            ['label'=>'Custom Reports'],
            ['label'=>'Marketing Settings'],
        ]
    ],

    [
        'id'=>'settings', 'label'=>'設定', 'icon'=>'fa-cog',
        'childs'=>[
            ['label'=>'Document Types'],
            ['label'=>'Predefined Properties'],
            ['label'=>'Predefined Asset Metadata'],
            ['label'=>'System Settings'],
            ['label'=>'Website'],
            ['label'=>'Web2Print'],
            ['label'=>'Users / Roles'],
            ['label'=>'Thumbnails'],
            ['label'=>'Object'],
            ['label'=>'Static Routes'],
            ['label'=>'Cache'],
            ['label'=>'Admin Translations'],
            ['label'=>'Tag Configuration'],
        ]
    ],

    [
        'id'=>'Search', 'label'=>'搜尋', 'icon'=>'fa-search',
        'childs'=>[
            ['label'=>'Documents'],
            ['label'=>'Assets'],
            ['label'=>'Objects'],
        ]
    ],
    
];