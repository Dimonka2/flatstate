<?php
namespace dimonka2\flatstate\Traits;

use dimonka2\flatstate\Flatstate;

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

    public static function getStateKey($state_id)
    {
        return Flatstate::getStateKey($state_id);
    }

    public static function formatState($state_id, $icon)
    {
        return Flatstate::formatState($state_id, $icon);
    }

    public function getState($state = 'state_id')
    {
        return self::getStateKey($this->{$state});
    }

    public function getState_($state = 'state_id')
    {
        return Flatstate::getState($this->{$state});
    }

    public function getStateState()
    {
        return self::getStateKey($this->state_id);
    }

    public function formatStateState($icon = true)
    {
        return self::formatState($this->state_id, $icon);
    }

    public function getStateType()
    {
        return self::getStateKey($this->type_id);
    }

    public function formatStateType($icon = true)
    {
        return self::formatState($this->type_id, $icon);
    }
}
