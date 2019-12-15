<?php
namespace dimonka2\flatstate;
use dimonka2\flatstate\State;

class FlatstateService
{
    private static $states = null;	
    
    private static function cachedAs()
    {
        return config('flatstate.cached_as', 'dimonka2.flatstates');
    }

    public function stateClass()
    {
        return State::class;
    }

    public static function formatState($state, $addIcon = false)
	{
		if (!is_object($state)) $state = static::getState($state);
		if (!is_object($state) ) 	{return "";}
		return ($addIcon ? $state->icon . " " : "") . $state->localizedName();
	}
    
    public static function clearCache()
    {
        Cache::forget(self::cachedAs());
    }
    
    protected static function getStates()
	{
        if (is_null(static::$states)) static::$states = 
            Cache::rememberForever(self::cachedAs(), function () {
			    return \App\Models\State::all();
		    });
		return static::$states;
	}

	public static function getState($id)
    {
        return static::getStates()->where('id', $id)->first();
	}	
	
	public static function getStateKey($id)
    {
		$state = self::getState($id);
		if(!is_object($state)) return null;
		return $state->state_key;
    }

	public static function selectState($key)
    {
        return static::getStates()->where('state_key', $key)->first();
	}	
	
    public static function selectStateId($key)
    {
        $state = self::selectState($key);
        if (!is_object($state)) return null;
        return $state->id;
    }	
	
	public static function selectStateList($category, $sort = true)
    {
        $states = static::getStates()->where('category', $category)->all();
		// try to localaize states
		// Log::info('Got some states', ['states' => $states]);
		$res = [];
		foreach ($states as $state) {
			$stateid = 'states.' . $state['state_key'];
			$newName = __($stateid);
			// Log::info('Item info', ['state' => $state, 'key' => $key, 'new name' => $newName]);
			if ($newName == $stateid) {
				$newName = $state['name'];
			}
//          if ($state->icon != "") {
//                $newName = $state->icon . ' ' . $newName;
//            }
			$res[$state['id']] = $newName;
		}
		if ($sort) {
			asort($res);
		}
		return $res;
    }

}