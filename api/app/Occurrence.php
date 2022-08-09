<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model{

	protected $table = 'omoccurrences';
	protected $primaryKey = 'occid';
	public $timestamps = false;

	protected $fillable = [	];
	protected $hidden = ['dynamicFields'];

	public function identification(){
		return $this->hasMany(OccurrenceIdentifications::class, 'occid', 'occid');
	}

	public function media(){
		return $this->hasMany(Media::class, 'occid', 'occid');
	}

	public function guid(){
	    return $this->hasOne(OccurrenceGuids::class, 'occid', 'occid');
	}

	public function portalPublications(){
		return $this->belongsToMany(portalpublications::class, 'portaloccurrences', 'occid', 'occid');
	}
}