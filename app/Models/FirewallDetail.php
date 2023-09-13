<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallDetail extends Model
{
    use HasFactory;
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallDetail extends Model
{
    use HasFactory;
    protected $table = 'firewall_details';
    protected $primaryKey = 'ip_id';

    public function fwipmany() {
        return $this->hasMany(FirewallRequest::class,'fw_number','fw_number');
    }
}
