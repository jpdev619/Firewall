<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeManagement extends Model
{
    use HasFactory;
    protected $table = 'change_management';
    protected $primaryKey = 'cm_id';

    public function userbasic() {
        return $this->belongsTo(UserBasic::class,'cm_user','user_xusern');
    }

    public function fw() {
        return $this->hasMany(FirewallRequest::class,'cm_fw_number','cm_number');
    }
}
