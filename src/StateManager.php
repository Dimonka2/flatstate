<?php

namespace dimonka2\flatstate;

use Illuminate\Support\Facades\Cache;

class StateManager
{
    private $states = null;
    private $cachedAs;
    private $stateClass;

    public function __construct()
    {
        $this->cachedAs = Flatstate::cachedAs();
        $this->stateClass = Flatstate::stateClass();
    }

	protected function getStates()
	{
        if (is_null($this->states)){ 
            $this->states = Cache::rememberForever($this->cachedAs, function () {
			    return $this->stateClass::all();
            });
        }
		return $this->states;
    }

    public function clearCache()
    {
        $this->states = null;
        Cache::forget($this->cachedAs);
    }

    public function getState($id)
    {
        return $this->getStates()->where('id', $id)->first();
    }

    public function getStateKey($id)
    {
        $state = $this->getState($id);
        if(!is_object($state)) return null;
        return $state->state_key;
    }


    public static function formatIcon($icon)
    {
        return '<i class="'. $icon . '"></i>';
    }

    public function getStateIcon($id, $formatted = true)
    {
        $state = $this->getState($id);
        if(!is_object($state)) return null;
        if (!$formatted) return $state->icon;
        return self::formatIcon($state->icon);
    }

    public function selectState($key)
    {
        if(is_array($key)){
            return $this->getStates()->whereIn('state_key', $key)->all();
        }
        return $this->getStates()->where('state_key', $key)->first();
    }

    public function selectStateId($key)
    {
        if(is_iterable($key)) {
            $ids = [];
            foreach ($key as $key_item) {
                $state = $this->selectState($key_item);
                if (is_object($state)) $ids[] = $state->id;
            }
            return $ids;
        }
        $state = $this->selectState($key);
        if (!is_object($state)) return null;
        return $state->id;
    }

    public function getStateList($type = null)
    {
        if($type) return $this->getStates()->where('state_type', $type)->all();
        return $this->getStates();
    }

    public function selectStateList($type, $sort = true)
    {
        $states = $this->getStates()->where('state_type', $type)->all();
		$res = [];
		foreach ($states as $state) {
			$stateid = 'states.' . $state['state_key'];
            // try to locaclize
            $newName = __($stateid);
			if ($newName == $stateid) {
				$newName = $state['name'];
			}

			$res[$state['id']] = $newName;
		}
		if ($sort) {
			asort($res);
		}
		return $res;
    }

	public function formatState($state, $addIcon = true)
	{
		if (!is_object($state)) $state = $this->getState($state);
		if (!is_object($state) ) 	{return "";}
		return ($addIcon ? self::formatIcon($state->icon) . "&nbsp;" : "") . $state->name;
    }

    public function closest_state($input, $collection, &$percent = null) {
        $input = strtolower($input);
        $shortest = -1;
        foreach ($collection as $state) {
            $lev = levenshtein($input, strtolower($state->name));

            if ($lev == 0) {
                $closest = $state;
                $shortest = 0;
                break;
            }

            if ($lev <= $shortest || $shortest < 0) {
                $closest  = $state;
                $shortest = $lev;
            }
        }

        $percent = 1 - levenshtein($input, strtolower($closest->name)) / max(strlen($input), strlen($closest->name));

        return $closest;
    }

    public function color($state, $default = 'dark')
    {
        if (!is_object($state)) $state = $this->getState($state);
        // debug($state);
        if (!is_object($state) or !isset($state->color) ) 	return $default;
        if ($state->color == 'default') return $default;
        return $state->color;
    }
}