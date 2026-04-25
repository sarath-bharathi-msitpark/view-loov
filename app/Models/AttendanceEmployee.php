<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'clock_in',
        'clock_out',
        'late',
        'early_leaving',
        'overtime',
        'total_rest',
        'created_by',
        
        'start_ride',
        'end_ride',
        'total_ride',
        'clock_in_images',
        'clock_in_latitude',
        'clock_in_longitude',
        
        'clock_out_images',
        'clock_out_latitude',
        'clock_out_longitude',
        'clock_in_location',
        'clock_out_location'
        
    ];

    public function employees()
    {
        return $this->hasOne('App\Models\Employee', 'user_id', 'employee_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }

    public function idleTimeOut()
    {
        return $this->hasMany(IdleTimeOut::class, 'attendance_id');
    }

    public function getTotalIdleDurationAttribute()
    {
        $totalSeconds = 0;
        foreach ($this->idleTimeOut as $idle) {
            if ($idle->start_time_and_date && $idle->end_time_and_date) {
                $start = \Carbon\Carbon::parse($idle->start_time_and_date);
                $end = \Carbon\Carbon::parse($idle->end_time_and_date);
                $totalSeconds += $end->diffInSeconds($start);
            }
        }
        return gmdate('H:i:s', $totalSeconds);
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return mixed|string
     */
    public function getAddress($latitude, $longitude)
    {
        $apiKey = env('MAP_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($data['status'] === 'OK') {
            $address = $data['results'][0]['formatted_address'] ?? null;
            return $address ?: 'Address not found';
        }

        return 'Address not found';
    }
}
