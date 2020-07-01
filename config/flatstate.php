<?php

// use dimonka2\flatform\State;

return [
    'cached_as' => 'dimonka2.flatstates',
    'migration' => [
        'table' => 'states',
        'enabled' => true,
    ],

    'fillable' => [
		'name',
        'icon',
        'descriptions',
        'color',
    ],

    // add models that uses states
    'models' => [],
];
