<?php

// use dimonka2\flatform\State;

return [
    'cached_as' => 'dimonka2.flatstates',
    'table' => 'states',
    'fillable' => [
		'name',	
        'icon',
        'descriptions',    
        'color',    
    ],

    // add models that uses states
    'models' => [],
];