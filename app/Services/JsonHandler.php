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
     * check structure json file
     *
     * @return bool
     */
    public function checkStructure()
    {
        $uploadedData = $this->getCurrentData();
        $required = ['last_update', 'currency'];
        $requiredCurrency = ['name', 'unit', 'currencycode', 'country', 'rate', 'change'];

        if ($this->checkArrayKeys($required, $uploadedData)) {
            foreach ($uploadedData['currency'] as $item) {
                if ($this->checkArrayKeys($requiredCurrency, $item) === false) {
                   return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @param $file
     */
     public function saveCurrentFile()
    {
        // write a file to directory

        $this->originalFile->move(config('app.files_path'), $this->fileName);

        // write a file to database
        File::create([
            'file' => $this->fileName
        ]);
    }

    /**
     * @return mixed
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

    /**
     * @param $required
     * @param $arr
     * @return bool
     */
    private function checkArrayKeys($required, $arr)
    {
        return (count(array_intersect_key(array_flip($required), $arr)) === count($required));
    }
}