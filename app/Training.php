<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    CONST TABLE_NAME = 'trainings';
    CONST TRAINING_ID = 'id';
    CONST TRAINING_TITLE = 'title';
    CONST TRAINING_DESCRIPTION = 'description';
    CONST TRAINING_FILE_SRC = 'file_src';
    CONST TRAINING_FILE_NAME = 'file_name';
    CONST ACTIVE = 'active';
    CONST CREATED_AT = 'created_at';
    CONST UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::TRAINING_ID, self::TRAINING_TITLE, self::TRAINING_DESCRIPTION, self::TRAINING_FILE_SRC,
        self::TRAINING_FILE_NAME, self::ACTIVE, self::CREATED_AT, self::UPDATED_AT,
    ];

    protected $hidden = [
        self::CREATED_AT, self::UPDATED_AT,
    ];

    /**
     * @return BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'companies_trainings', 'training_id','company_id');
    }
}
