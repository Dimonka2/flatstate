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
    private static $formatFunction;

	public static function getStateFillable(): array
	{
		if(!static::$fillable) static::$fillable = static::config('fillable', static::fillable);
		return static::$fillable;
	}

	public static function getStateTable()
	{
		if(!static::$table) static::$table = static::config('migration.table', 'states');
		return static::$table;
	}

	public static function config($path, $default = null)
    {
        return config('flatstate.' . $path, $default);
    }

    public static function cachedAs()
    {
        return self::config('cached_as', 'dimonka2.flatstates');
    }

    public static function stateClass()
    {
        return self::config('state_class', State::class);
	}

	protected static function managerClass()
    {
        return self::config('manager_class', StateManager::class);
	}

	protected static function manager()
	{
		return app('flatstates');
	}

    public static function formatState($state, $addIcon = false)
	{
		if (!is_object($state)) $state = static::getState($state);
        if($addIcon instanceof \Closure) {
            return $addIcon($state);
        }
        if(static::$formatFunction instanceof \Closure) {
            return static::$formatFunction($state, $addIcon);
        }
		if (!is_object($state) ) 	{return "";}
		return ($addIcon ? static::formatIcon($state->icon) . "&nbsp;" : "") . $state->name;
	}

    public static function validationString(string $category, $required = true) 
    {
        $keys = collect(static::manager()->getStateList($category))
            ->map(fn ($item) => $item->state_key)->all();
        return 'string|' . 
            ( $required ? 'required' : 'nullable|sometimes' ) .
            '|in:' . join(',', $keys);
    }

    public static function formatIcon($icon)
    {
        return '<i class="'. $icon . '"></i>';
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

    public static function getStateList($type = null)
    {
        return static::manager()->getStateList($type);
    }

    public static function color($state, $default = 'dark')
    {
        return static::manager()->color($state, $default);
    }


    /**
     * Set the value of formatFunction
     *
     * @return  self
     */
    public static function setFormatFunction($formatFunction)
    {
        static::$formatFunction = $formatFunction;
    }
}
