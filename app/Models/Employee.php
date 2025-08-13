<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'education',
        'joining_year',
        'city',
        'payment_tier',
        'age',
        'gender',
        'ever_benched',
        'experience_in_current_domain',
        'leave_or_not'
    ];

    protected $casts = [
        'ever_benched' => 'boolean',
        'leave_or_not' => 'boolean',
    ];

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByEducation($query, $education)
    {
        return $query->where('education', $education);
    }

    public function scopeBenched($query)
    {
        return $query->where('ever_benched', true);
    }

    public function scopeAtRisk($query)
    {
        return $query->where('leave_or_not', true);
    }
}
