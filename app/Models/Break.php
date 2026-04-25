namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Break extends Model
{
    use HasFactory;

    protected $table = 'breaks';

    protected $fillable = [
        'punch_id',
        'user_id',
        'type',
        'break_started_at',
        'break_ended_at',
        'duration',
    ];
}
