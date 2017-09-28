<?php

namespace Encore\Admin\Translation;

use Illuminate\Database\Eloquent\Model;

class TranslationModel extends Model
{
    const STATUS_SAVED = 0;

    const STATUS_CHANGED = 1;

    protected $fillable = ['locale', 'group', 'key'];

    /**
     * Settings constructor.
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection(config('admin.database.connection') ?: config('database.default'));

        $this->setTable(config('admin.extensions.translation.table', 'laravel_translations'));
    }

    public static function boot()
    {
        parent::boot();

        static::updating(function (Model $model) {
            if ($model->attributes['value'] != $model->original['value']) {
                $model->status = static::STATUS_CHANGED;
            }
        });
    }
}
