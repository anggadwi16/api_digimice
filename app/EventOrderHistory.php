<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class EventOrderHistory extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = "event_order_history";

    protected $fillable = [
        'status','event_id','event_paket_event_id','id_invoice','user_id'
    ];

    public function paket(){
    	return $this->belongsTo('App\Paket','event_paket_event_id','id');
    }

   
}