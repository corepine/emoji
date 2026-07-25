<?php

return [
    'locale' => 'en',

    'theme' => 'system',

    'color' => 'emerald',

    'columns' => 8,

    'recent_limit' => 24,

    'quick_reactions' => ['👍', '❤️', '😂', '😮', '😢', '🙏'],

    'storage' => [
        'recent' => 'corepine.emoji.recent',
        'reactions' => 'corepine.emoji.reactions',
    ],

    'events' => [
        'dispatch' => [
            'selected' => 'emoji.selected',
            'reaction_selected' => 'emoji.reaction-selected',
        ],
    ],
];
