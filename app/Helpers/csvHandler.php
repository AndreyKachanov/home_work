<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 17:07
 */

namespace App\Helpers;

use App\Helpers\Contracts\currencyFiles;
use App\File;

class csvHandler implements currencyFiles
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

        $data['currency'] = \Excel::load((config('app.files_path') . $this->fileName))->all()->toArray();

        return $data;

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
        $newFileName = 'currency_' . date('Y_m_d_H_i_s');
        $newFilePath = config('app.files_path_new');

        \Excel::create($newFileName, function($excel) use($newData) {

            $excel->sheet('Sheetname', function($sheet) use($newData) {
                $sheet->fromArray($newData['currency']);
            });

        })->save('csv', $newFilePath );

        return $newFileName . ".csv";
    }
}