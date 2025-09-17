<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'type'       => $type, // avatar,post_image, thumbnail, banner
            'path'       => $path,
            'mime'       => $mime,
            'size'       => $size,
            'width'      => $width,
            'height'     => $height,
        ]);
    }
}
