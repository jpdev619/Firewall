<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeManagementStages extends Model
{
    use HasFactory;
    protected $table = 'change_management_stages';
    protected $primaryKey = 'cmstg_id';

    public function userbasic() {
        return $this->belongsTo(UserBasic::class,'cmstg_user','user_xusern');
    }
    public function comment() {
        return $this->hasMany(ChangeManagement::class,'cm_number','cm_number');
    }
}
