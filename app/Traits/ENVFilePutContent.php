<?php

namespace App\Traits;

trait ENVFilePutContent
{
    public function dataWriteInENVFile($key, $value)
    {
        $path = app()->environmentFilePath();
        $content = file_get_contents($path);

        $line = $key.'='.$this->formatEnvValue($value);

        if (preg_match('/^'.preg_quote($key, '/').'=/m', $content)) {
            $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $content);
        } else {
            $content = rtrim($content, "\r\n")."\n".$line."\n";
        }

        file_put_contents($path, $content);
    }

    /**
     * Quote .env values that contain spaces or special characters.
     */
    protected function formatEnvValue($value): string
    {
        if ($value === null) {
            return '';
        }

        $string = (string) $value;
        if ($string === '') {
            return '';
        }

        if (preg_match('/[\s#\'"]/u', $string) || str_contains($string, '\\')) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $string).'"';
        }

        return $string;
    }
}
