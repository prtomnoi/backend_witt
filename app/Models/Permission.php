<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = [
        'role_id',
        'table',
        'create',
        'update',
        'delete',
        'view',
    ];

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }
}
