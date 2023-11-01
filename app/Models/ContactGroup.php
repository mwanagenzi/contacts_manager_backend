<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'contact_id'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
