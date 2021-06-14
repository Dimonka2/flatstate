<?php
namespace dimonka2\flatstate\Traits;

use dimonka2\flatstate\Flatstate;

trait Stateable
{
   	/* protected $states =  [
	    'state_id' => [
            'type' => 'projects',
            'default' => 'pr_active',
			['name' => 'Active', 'key' => 'pr_active', 'descriptions' => '..', 'icon' => 'fa fa-info', 'color' => 'danger',],
		],
	 ] */
    // protected $states = [];

    public function initializeStatable()
    {
        $this->setStateDefaults();
    }

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
        return static::getStateKey($this->{$state});
    }

    public function getState_($state = 'state_id')
    {
        return Flatstate::getState($this->{$state});
    }

    public function getStateState()
    {
        return static::getStateKey($this->state_id);
    }

    public function formatStateState($icon = true)
    {
        return static::formatState($this->state_id, $icon);
    }

    public function getStateType()
    {
        return static::getStateKey($this->type_id);
    }

    public function formatStateType($icon = true)
    {
        return static::formatState($this->type_id, $icon);
    }

    private function setStateDefaults(): void
    {
        foreach ($this->getStates() as $field => $config) {
            if ($this->{$field} !== null) {
                continue;
            }
            if (!array_key_exists('default', $config)) {
                continue;
            }
            $this->{$field} = Flatstate::selectStateId($config['default']);
        }
    }
}
