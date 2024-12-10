<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    // The attributes that are mass assignable
    protected $fillable = [
        'planname',
        'price',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'price' => 'decimal:2',
    ];

    // The dates for the soft delete feature
    protected $dates = ['deleted_at'];

    // The user who created the plan
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // The user who updated the plan
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // The user who deleted the plan
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
