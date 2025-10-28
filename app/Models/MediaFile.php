<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;

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
        // 🎥 1️⃣ 動画の場合はサムネイル生成
        $thumbnail = null;
        if (str_starts_with($mime, 'video/')) {
            // try {
                $tempVideoPath = tempnam(sys_get_temp_dir(), 'video_');
                file_put_contents($tempVideoPath, file_get_contents($file->getRealPath()));
        
                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe',
                ]);
                $video = $ffmpeg->open($tempVideoPath);
        
                // 🔄 MOV → MP4 (H.264 + AAC) 変換
                $convertedPath = tempnam(sys_get_temp_dir(), 'converted_') . '.mp4';
                $video->save(new \FFMpeg\Format\Video\X264('aac', 'libx264'), $convertedPath);
        
                // 再アップロード
                $newFile = new \Illuminate\Http\File($convertedPath);
                $path = Storage::disk($disk)->putFile($dir, $newFile);
        
                // 🎞 サムネイル生成（1秒目）
                $tempThumbPath = storage_path('app/temp_thumb_' . uniqid() . '.jpg');
                $video->frame(TimeCode::fromSeconds(1))->save($tempThumbPath);
                $thumbPath = Storage::disk($disk)->putFile($dir . '/thumbnails', new \Illuminate\Http\File($tempThumbPath));
        
                $thumbnail = self::create([
                    'owner_type' => get_class($owner),
                    'owner_id'   => $owner->getKey(),
                    'type'       => 'thumbnail',
                    'path'       => $thumbPath,
                    'mime'       => 'image/jpeg',
                ]);
        
                @unlink($tempVideoPath);
                @unlink($convertedPath);
                @unlink($tempThumbPath);
        
            // } catch (\Throwable $e) {
            //     \Log::error('Video convert/thumbnail failed: ' . $e->getMessage());
            // }
        }

        return self::create([
            'owner_type' => get_class($owner),
            'owner_id'   => $owner->getKey(),
            'type'       => $type,
            'path'       => $path,
            'mime'       => $mime,
            'size'       => $size,
            'width'      => $width,
            'height'     => $height,
            'thumbnail_media_id' => $thumbnail?->id,
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
