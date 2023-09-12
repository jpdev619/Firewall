<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallRequest extends Model
{
    use HasFactory;

    protected $table = 'firewall_request';
    protected $primaryKey = 'fw_id';

    public function userbasic() {
        return $this->belongsTo(UserBasic::class,'fw_user','user_xusern');
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
    
    public function fwStages() {
        return $this->hasMany(ChangeManagementStages::class,'cmstg_cmnumber','fw_number')->orderBy('cmstg_id','desc');
    }
    public function fwStagesAsc() {
        return $this->hasMany(ChangeManagementStages::class,'cmstg_prnumber','fw_number');
    }
    public function fwUpload() {
        return $this->hasMany(ChangeManagementUploads::class,'cm_number','fw_number');
    }
    public function fwipmany() {
        return $this->hasMany(FirewallDetail::class,'fw_number','fw_number');
    }
}
clear