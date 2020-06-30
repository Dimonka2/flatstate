<?php
namespace dimonka2\flatstate\Traits;


trait Stateable
{
   	/* protected $states =  [
	    'state_id' => ['type' => 'projects',
			['name' => 'Active', 'key' => 'pr_active', 'descriptions' => '..', 'icon' => 'fa fa-info', 'color' => 'danger',],
		],
	 ] */
    // protected $states = [];

    public function getStates()
	{
		return $this->states ?? [];
	}
}
