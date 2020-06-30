<?php

namespace dimonka2\flatstate;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
	protected $primaryKey = 'id';	
    protected $fillable;	
	
	public $timestamps = true;
	protected $guarded  = ['id', 'state_type', 'state_key'];
	
    public function __construct( array $attributes = [] )
    {
        $this->table = Flatstate::getStateTable();
        $this->fillable = Flatstate::getStateFillable();

        parent::__construct($attributes);

    }
	
}
