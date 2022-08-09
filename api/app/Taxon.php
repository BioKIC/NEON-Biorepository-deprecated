<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxon extends Model{

	protected $table = 'taxa';
	protected $primaryKey = 'tid';
	protected $hidden = [ 'phyloSortSequence', 'nomenclaturalStatus', 'nomenclaturalCode', 'statusNotes', 'hybrid' ];
	protected $fillable = [  ];

    public function taxStatuses(){
		return $this->hasMany('App\Taxstatus', 'tid', 'TID');
	}

}
