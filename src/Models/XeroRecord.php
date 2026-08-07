<?php

namespace Dcodegroup\XeroIntegration\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Tracks the mapping between local recordable models and their corresponding Xero records.
 *
 * @property int $id
 * @property int $recordable_id
 * @property string $recordable_type
 * @property string $xero_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static> query()
 */
class XeroRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'recordable_id',
        'recordable_type',
        'xero_id',
    ];

    public function recordable()
    {
        return $this->morphTo();
    }
}
