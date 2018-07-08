<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 10:44
 */

namespace App\Providers;


use App\Services\CsvHandler;
use App\Services\JsonHandler;
use App\Services\XmlHandler;
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
        $this->app->bind('App\Contracts\CurrencyFiles', function ($app, $params) {

            $originalFile = $params['file'];
            $ext = $originalFile->getClientOriginalExtension();

            // check file extention and get handler
            switch ($ext) {
                case 'json':
                    return new JsonHandler($originalFile);
                    break;
                case 'csv':
                    return new CsvHandler($originalFile);
                    break;
                case 'xml': return new XmlHandler($originalFile); break;
                default:
                    return false;
            }
        });

    }
}