<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occurrence extends Model{

	protected $table = 'omoccurrences';
	protected $primaryKey = 'occid';
	public $timestamps = false;

	protected $fillable = [	];
	protected $hidden = ['dynamicFields'];

	public function collection(){
		return $this->belongsTo(Collection::class, 'collid', 'collid');
	}

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
		return $this->belongsToMany(PortalPublications::class, 'portaloccurrences', 'occid', 'pubid')->withPivot('targetOccid');;
	}
}