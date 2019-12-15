<?php

namespace dimonka2\flatstate;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
	protected $primaryKey = 'id';
	
	protected $fillable = [
		'name',	
        'icon',
        'descriptions'
    ];
	
	public $timestamps = true;
	protected $guarded  = array('id', 'category', 'state_key');
    
    public function __construct( array $attributes = [] )
    {
        $this->table = config('flatstate.table', 'states');
        $this->fillable = config('flatstate.fillable', $this->fillable);

        parent::__construct($attributes);

    }
	
	public function localizedName()
	{
		$loc_name = 'states.' . $this->state_key;
		$newName = __($loc_name);
		if ($newName == $loc_name) {
			$newName = $this->name;
		}
		return $newName;
	}
	
}
