<?php

namespace App\Helpers;

class FilePath
{
    // Folder Names
    const FOLDER_BANNERS = 'banners';
    const FOLDER_ADVERTISEMENTS = 'advertisements';
    const FOLDER_NOTIFICATIONS = 'notifications';

    // Base Path for Storage (relative to public)
    const STORAGE_ROOT = 'storage';

    /**
     * Get the full URL for a stored file.
     * 
     * @param string|null $path Relative path from storage root
     * @return string|null
     */
    public static function getUrl(?string $path)
    {
        if (!$path) {
            return null;
        }

        // Normalize slashes
        $path = str_replace('\\', '/', $path);

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Clean up path if it starts with storage/ or uploads/
        $cleanPath = self::cleanPath($path);

        return asset(self::STORAGE_ROOT . '/' . $cleanPath);
    }

    /**
     * Clean the path by removing common prefixes.
     */
    public static function cleanPath(string $path)
    {
        if (str_starts_with($path, 'uploads/')) {
            return substr($path, 8);
        }
        if (str_starts_with($path, 'storage/')) {
            return substr($path, 8);
        }
        if (str_starts_with($path, 'public/')) {
            return substr($path, 7);
        }
        return $path;
    }

    /**
     * Extract relative path from a URL or path for storage operations.
     */
    public static function getRelativePath(string $path)
    {
        $path = str_replace('\\', '/', $path);
        
        if (str_contains($path, '/storage/')) {
            $parts = explode('/storage/', $path);
            return end($parts);
        }
        if (str_contains($path, '/uploads/')) {
            $parts = explode('/uploads/', $path);
            return end($parts);
        }
        if (str_starts_with($path, 'storage/')) {
            return substr($path, 8);
        }
        if (str_starts_with($path, 'uploads/')) {
            return substr($path, 8);
        }
        
        return $path;
    }

    /**
     * Get the folder path for storing files.
     * This is used when saving files to the disk.
     */
    public static function getFolder(string $folder)
    {
        return $folder;
    }
}
