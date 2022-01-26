<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PortalIndex extends Model
{
    protected $table = 'portalindex';
    protected $primaryKey = 'portalIndexID';
    protected $fillable = ['portalName', 'acronym', 'portalDescription', 'urlRoot', 'symbVersion', 'guid', 'manager', 'managerEmail', 'primaryLead', 'primaryLeadEmail', 'notes'];
    protected $guarded = [];
    protected $hidden = ['securityKey'];

    public function ompublications()
    {
        return $this->hasMany('App\Ompublication', 'portalIndexID', 'portalIndexID');
    }

    public function ompublicationoccurlinks()
    {
        return $this->hasMany('App\Ompublicationoccurlink', 'portalIndexID', 'portalIndexID');
    }
}
