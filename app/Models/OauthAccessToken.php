<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OauthAccessToken extends Model
{
    protected $table = 'oauth_access_tokens';

    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ Include 'token' if it's stored in plain text and not hashed
    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked',
        'created_at',
        'updated_at',
        'expires_at',
        'token', // ✅ Add this line
    ];

    // ✅ Ensure it's not hidden
    protected $hidden = [
        // 'token', ← Make sure 'token' is NOT in this array
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
