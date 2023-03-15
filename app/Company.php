<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    CONST TABLE_NAME = 'companies';
    CONST COMPANY_ID = 'id';
    CONST COMPANY_NAME = 'name';
    CONST COMPANY_LICENSE_ID = 'license_id';
    CONST COMPANY_LICENSE_START = 'license_start';
    CONST ACTIVE = 'active';

    CONST DEFAULT_COMPANY = 'default company';

    protected $fillable = [
        self::COMPANY_ID, self::COMPANY_NAME, self::COMPANY_LICENSE_ID,
        self::COMPANY_LICENSE_START, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT,
    ];

    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * @return BelongsTo
     */
    public function license()
    {
        return $this->belongsTo(License::class);
    }

    /**
     * @return BelongsToMany
     */
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'companies_trainings', 'company_id', 'training_id');
    }
}
