<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 10:44
 */

namespace App\Providers;


use App\Helpers\csvHandler;
use App\Helpers\jsonHandler;
use App\Helpers\xmlHandler;
use Illuminate\Support\ServiceProvider;

class SaveFileProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Helpers\Contracts\currencyFiles', function ($app, $params) {

            $originalFile = $params['file'];
            $ext = $originalFile->getClientOriginalExtension();

            // check file extention and get handler
            switch ($ext) {
                case 'json':
                    return new jsonHandler($originalFile);
                    break;
                case 'csv':
                    return new csvHandler($originalFile);
                    break;
                case 'xml': return new xmlHandler($originalFile); break;
                default:
                    return false;
            }
        });

    }
}