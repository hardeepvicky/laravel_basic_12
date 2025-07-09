<?php

namespace App\Models;

use App\Exceptions\UserException;
use App\Traits\CacheTrait;
use App\Traits\CommonModelTrait;
use App\Traits\CommonMysqlModelTrait;
use App\Traits\ModelListTrait;
use App\Traits\MysqlTableInfoTrait;
use Illuminate\Notifications\DatabaseNotification; // Import the base class
use Illuminate\Support\Facades\Auth;

class Notification extends DatabaseNotification // Extend DatabaseNotification for convenience
{
    use CacheTrait, ModelListTrait, CommonModelTrait, MysqlTableInfoTrait, CommonMysqlModelTrait;
    
    public static array $child_model_class = [];

    protected static array $unique_fields = [];

    protected static array $file_fields = [];

    public array $custom_errors = [];

    protected $fillable = [
        'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at',
        'sub_type', 'from_user_id'
    ];

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array', // Cast the data column to an array
        'read_at' => 'datetime',
    ];

    const SUB_TYPE_INFO = "INFO";
    const SUB_TYPE_WARNING = "WARNING";
    const SUB_TYPE_CRITICAL = "CRITICAL";

    public static function classDisplayName(): string
    {
        $class_name = str_class_name_to_human_text(static::class);

        return $class_name;
    }

    public static function getCacheKey(): string
    {
        $str = static::class;

        return static::optimizeCacheKey($str);
    }
    
    public static function getFileFields()
    {
        return static::$file_fields;
    }

    public static function getListFields()
    {
        return [];
    }

    public static function getUniqueFields()
    {
        return static::$unique_fields;
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $auth_user = Auth::user();

            if ($auth_user) {
                $model->created_by = $auth_user->id;
            }
            
            if ($model->data && is_array($model->data))
            {
                $model->from_user_id = $model->data['from_user_id'] ??  0;
                $model->sub_type = $model->data['sub_type'] ??  "";
            }

            // dd($model->getAttributes());
        });

        self::created(function ($model) {            
        });

        self::updating(function ($model) {
        });

        self::updated(function ($model) {
        });

        self::deleting(function ($model) {
        });

        self::deleted(function ($model) {            
        });
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, "from_user_id");
    }
}