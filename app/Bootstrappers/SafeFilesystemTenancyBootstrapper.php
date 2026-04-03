<?php

declare(strict_types=1);

namespace App\Bootstrappers;

use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;

/**
 * Stancl resolves bootstrappers fresh for bootstrap() vs revert(); originalPaths on FilesystemTenancyBootstrapper
 * may be empty on revert → "Undefined array key local" (tenancy.filesystem.disks). Fallback to central roots
 * captured once at application boot.
 */
class SafeFilesystemTenancyBootstrapper extends FilesystemTenancyBootstrapper
{
    /** @var array<string, mixed> */
    protected static array $centralDiskRoots = [];

    public static function seedCentralDiskRoot(string $disk, mixed $root): void
    {
        if (! array_key_exists($disk, static::$centralDiskRoots)) {
            static::$centralDiskRoots[$disk] = $root;
        }
    }

    public function revert(): void
    {
        $this->app->useStoragePath($this->originalPaths['storage']);

        $this->app['config']['app.asset_url'] = $this->originalPaths['asset_url'];
        $this->app['url']->setAssetRoot($this->app['config']['app.asset_url']);

        Storage::forgetDisk($this->app['config']['tenancy.filesystem.disks']);
        foreach ($this->app['config']['tenancy.filesystem.disks'] as $disk) {
            $root = null;
            if (array_key_exists($disk, $this->originalPaths['disks'])) {
                $root = $this->originalPaths['disks'][$disk];
            } elseif (array_key_exists($disk, static::$centralDiskRoots)) {
                $root = static::$centralDiskRoots[$disk];
            }
            if ($root !== null) {
                $this->app['config']["filesystems.disks.{$disk}.root"] = $root;
            }
        }
    }
}
