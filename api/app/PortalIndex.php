<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PortalIndex extends Model
{
    protected $table = 'portalindex';
    protected $primaryKey = 'portalID';
    protected $fillable = ['portalName', 'acronym', 'portalDescription', 'urlRoot', 'symbiotaVersion', 'guid', 'manager', 'managerEmail', 'primaryLead', 'primaryLeadEmail', 'notes'];
    protected $guarded = [];
    protected $hidden = ['securityKey'];
    public $timestamps = false;

    public function portalpublications(){
        return $this->hasMany('App\portalpublications', 'portalID', 'portalID');
    }

    public function registeredOccurrences(){
        return $this->hasMany('App\portaloccurrences', 'portalID', 'portalID');
    }
}
