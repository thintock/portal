<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    use HasFactory;

    protected $table = 'media_files';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'type',
        'path',
        'mime',
        'size',
        'alt',
        'width',
        'height',
        'duration',
        'thumbnail_media_id',
    ];

    /**
     * 所有者（ポリモーフィックリレーション）
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * サムネイル（自己参照）
     */
    public function thumbnail()
    {
        return $this->belongsTo(MediaFile::class, 'thumbnail_media_id');
    }

    /**
     * サムネイルを持つ動画（逆リレーション）
     */
    public function video()
    {
        return $this->hasOne(MediaFile::class, 'thumbnail_media_id');
    }
    
    // ファイルのアップロード
    public static function uploadAndCreate(
        \Illuminate\Http\UploadedFile $file,
        Model $owner,
        string $type = null,
        string $disk = null,
        string $dir = 'uploads'
    ): self {
        $disk = $disk ?? config('filesystems.default'); // s3 or public
        $path = $file->store($dir, $disk);

        $mime = $file->getMimeType();
        $size = $file->getSize();
        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        return self::create([
            'owner_type' => get_class($owner),
            'owner_id'   => $owner->getKey(),
            'type'       => $type,
            'path'       => $path,
            'mime'       => $mime,
            'size'       => $size,
            'width'      => $width,
            'height'     => $height,
        ]);
    }
    
    // ファイルの署名付きリンク生成
    public function getUrlAttribute(): string
    {
        $disk = config('filesystems.default');

        // S3なら署名付きURL
        if ($disk === 's3') {
            return Storage::disk($disk)->temporaryUrl(
                $this->path,
                now()->addMinutes(5)
            );
        }

        // publicディスクなら直リンク
        return Storage::disk($disk)->url($this->path);
    }
    
    public function relations()
    {
        return $this->hasMany(MediaRelation::class, 'media_file_id');
    }
    
    public function attachedTo()
    {
        return $this->morphToMany(MediaRelation::class, 'mediable');
    }
}
