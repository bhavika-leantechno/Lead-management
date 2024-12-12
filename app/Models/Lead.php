<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes for soft delete functionality

    // Table name (optional, if it's different from the pluralized form of the model)
    protected $table = 'leads';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'lead_type',
        'company_name',
        'email',
        'location',
        'some_text',
        'cr_file',
        'cc_file',
        'tl_file',
        'processing_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'user_id',
        'status',
        'level',
        'service_type',
        'lead_type',
        'phone_number',
        'agent_id',
        'stage_movement',
        'disposition',
        'remarks',
        'attachment',
        'next_follow_up_date',
        'hours',
        'change_status',
        'plan_id'
    ];

    // The attributes that should be hidden for arrays (e.g., passwords, tokens)
    protected $hidden = [];

    // The attributes that should be cast to native types (e.g., dates, booleans)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // For soft deletes
    ];

    // Define relationships (if any)
    // For example, a Lead belongs to a User:
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // If you have an admin user that created the lead
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // If you have an admin user that updated the lead
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // If you have an admin user that deleted the lead (in case of soft delete)
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Additional methods to handle file uploads, status changes, etc. could be added here.
}
