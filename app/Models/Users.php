<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'tel',
        'email_verified_at',
        'tel_verified_at',
        'auth_type',
        'auth_id_token',
        'six_pin',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function checkSuperAdmin()
    {
        $data = Roles::select('name')->where('id', $this->role_id)->first();
        return $data?->name == 'SUPER_ADMIN' ? true : false;
    }

    public function checkAdmin()
    {
        $data = Roles::select('name')->where('id', $this->role_id)->first();
        return $data?->name == 'ADMIN' ? true : false;
    }

    public function checkRider()
    {
        $data = Roles::select('name')->where('id', $this->role_id)->first();
        return $data?->name == 'RIDER' ? true : false;
    }

    public function checkUser()
    {
        $data = Roles::select('name')->where('id', $this->role_id)->first();
        return $data?->name == 'USER' ? true : false;
    } 

}
