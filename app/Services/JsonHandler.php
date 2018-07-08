<?php

namespace App\Services;

use App\Services\Contracts\CurrencyFiles;
use App\File;

class JsonHandler implements CurrencyFiles
{
    private $originalFile;

    private $fileName;

    public function __construct($originalFile)
    {
        $this->originalFile = $originalFile;
        $this->fileName = md5(uniqid()) . '.' . $this->originalFile->getClientOriginalExtension();
    }

    /**
     * @param $file
     */
     function saveCurrentFile()
    {
        // write a file to directory

        $this->originalFile->move(config('app.files_path'), $this->fileName);

        // write a file to database
        File::create([
            'file' => $this->fileName
        ]);
    }

    /**
     * @param $file
     */
    public function getCurrentData()
    {
        return json_decode(file_get_contents(config('app.files_path') . $this->fileName), true);
    }

    /**create new json file
     * @return string
     */
    public function updateData()
    {
        date_default_timezone_set('Europe/Kiev');
        $data = $this->getCurrentData();

        // get updated array
        $newData = CurrencyService::updateCurrencyRate($data);

        $newFileName = 'currency_' . date('Y_m_d_H_i_s') . '.json';
        $newFilePath = config('app.files_path_new') . $newFileName;

        $file = fopen($newFilePath, 'w+');
        fwrite($file, json_encode($newData));
        fclose($file);

        return $newFileName;
    }
}