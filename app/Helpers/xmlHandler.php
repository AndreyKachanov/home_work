<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 21:11
 */

namespace App\Helpers;


use App\Helpers\Contracts\currencyFiles;
use App\File;
use Spatie\ArrayToXml\ArrayToXml;

class xmlHandler implements currencyFiles
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

    /**Get current data
     * @return array
     */
    public function getCurrentData()
    {
        $xmlFile = file_get_contents(config('app.files_path') . $this->fileName);

        $ob = simplexml_load_string($xmlFile);
        $json  = json_encode($ob);

        $jsonDecode = json_decode($json, true);

        return CurrencyService::changeKeyCaseMultidimensionArray($jsonDecode, 'CASE_LOWER');

    }

    /**create new json file
     * @return string
     */
    public function updateData()
    {
        date_default_timezone_set('Europe/Kiev');
        $data = $this->getCurrentData();

        $newData = CurrencyService::updateCurrencyRate($data, $caseLower = true);

        $xml = ArrayToXml::convert($newData, 'CURRENCIES');

        $newFileName = 'currency_' . date('Y_m_d_H_i_s') . '.xml';
        $newFilePath = config('app.files_path_new') . $newFileName;

        file_put_contents($newFilePath, $xml);

        return $newFileName;
    }
}