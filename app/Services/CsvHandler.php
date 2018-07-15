<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 17:07
 */

namespace App\Services;

use App\Services\Contracts\CurrencyFiles;
use App\File;

class csvHandler implements CurrencyFiles
{
    private $originalFile;

    private $fileName;

    public function __construct($originalFile)
    {
        $this->originalFile = $originalFile;
        $this->fileName = md5(uniqid()) . '.' . $this->originalFile->getClientOriginalExtension();
    }

    /**
     * check structure csv file
     *
     * @return bool
     */
    public function checkStructure()
    {
        $uploadedData = $this->getCurrentData();

        $required = ['name', 'unit', 'currencycode', 'country', 'rate', 'change', 'last_update'];

        foreach ($uploadedData['currency'] as $item) {
            if ($this->checkArrayKeys($required, $item) === false) {
                return false;
            }
        }

        return true;
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
     * @param $file
     */
    public function getCurrentData()
    {
        // $data['currency'] = \Excel::load((config('app.files_path') . $this->fileName))->all()->toArray();

        $file = fopen((config('app.files_path') . $this->fileName), 'r');

        if ($file) {
            $keys = fgetcsv($file, 0, ',');

            while (($l = fgetcsv($file, 0, ',')) !== false) {
                $data['currency'][] = array_combine($keys, $l);
            }

            fclose($file);
            return $data;
        }

        return false;
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
        // file name
        $fileName = 'currency_' . date('Y_m_d_H_i_s') . ".csv";
        // full file path
        $filePath = config('app.files_path_new') . $fileName;

        $keys = $newData['currency'][0];

        $file = fopen($filePath, 'w');
        fputcsv($file, $keys);

        foreach (array_slice($newData['currency'], 1) as $item) {
            fputcsv($file, array_values($item));
        }

        fclose($file);

//        \Excel::create($newFileName, function($excel) use($newData) {
//
//            $excel->sheet('Sheetname', function($sheet) use($newData) {
//                $sheet->fromArray($newData['currency']);
//            });
//
//        })->save('csv', $newFilePath );

        return $fileName;
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