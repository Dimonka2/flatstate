<?php
namespace dimonka2\flatstate;
use dimonka2\flatstate\State;

class FlatstateService
{
	protected const fillable = [
		'name',	
        'icon',
		'descriptions',
		'color',
	];

	private static $table;
	private static $fillable;

	private static $manager;	
	
	public static function getStateFillable(): array
	{
		if(!static::$fillable) static::$fillable = static::config('flatstate.fillable', static::fillable);
		return static::$fillable;
	}

	public static function getStateTable()
	{
		if(!static::$table) static::$table = static::config('flatstate.table', 'states');
		return static::$table;
	}

	public static function config($path, $default = null)
    {
        return config($path, $default);
    }
    
    private static function cachedAs()
    {
        return self::config('flatstate.cached_as', 'dimonka2.flatstates');
    }

    protected function stateClass()
    {
        return self::config('flatstate.state_class', State::class);
	}
	
	protected function managerClass()
    {
        return self::config('flatstate.manager_class', StateManager::class);
	}
	
	protected static function manager()
	{
		if(!static::$manager) {
			$class = static::managerClass();
			static::$manager = new $class(static::stateClass(), static::cachedAs());
		}
		return static::$manager;
	}

    public static function formatState($state, $addIcon = false)
	{		
		return static::manager()->formatState($state, $addIcon);
	}
    
    public static function clearCache()
    {
        return static::manager()->clearCache();
    }
    
	public static function getState($id)
    {
        return static::manager()->getState($id);
	}	
	
	public static function getStateKey($id)
    {
		return static::manager()->getStateKey($id);
    }

	public static function selectState($key)
    {
        return static::manager()->selectState($key);
	}	
	
    public static function selectStateId($key)
    {
        return static::manager()->selectStateId($key);
    }	
	
	public static function selectStateList($category, $sort = true)
    {
        return static::manager()->selectStateList($category, $sort);
    }

}