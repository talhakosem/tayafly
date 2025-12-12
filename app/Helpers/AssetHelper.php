<?php

if (!function_exists('admin_asset')) {
    /**
     * Admin panel assets için yardımcı fonksiyon
     *
     * @param string $path
     * @return string
     */
    function admin_asset($path)
    {
        return asset('admin/' . ltrim($path, '/'));
    }
}

if (!function_exists('frontend_asset')) {
    /**
     * Frontend assets için yardımcı fonksiyon
     *
     * @param string $path
     * @return string
     */
    function frontend_asset($path)
    {
        return asset('frontend/' . ltrim($path, '/'));
    }
}

