<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;



class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'mobilenumber',
        'email',
        'qr_code',
        'password',
        'expiredate',
        'status',
        'is_deleted',
        'approve_status',
        'user_type',
        'profile_picture',
        'assigned_report',
        'approve_freelancer',
        'assigned_agent'
    ];
    protected $dates = [
        'expiredate',
        'deleted_at',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    * Get the identifier that will be stored in the subject claim of the JWT.
    *
    * @return mixed
    */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically, the primary key of the user
    }
    
    /**
     * Return a key-value array containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // You can add custom claims here
    }

     /**
     * User is a one-to-many relationship with Lead.
     * A user can have many leads.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
     /**
     * User has a one-to-many relationship with created leads (for tracking created_by).
     */
    public function createdLeads()
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    /**
     * User has a one-to-many relationship with updated leads (for tracking updated_by).
     */
    public function updatedLeads()
    {
        return $this->hasMany(Lead::class, 'updated_by');
    }

}
