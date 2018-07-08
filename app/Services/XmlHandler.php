<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 21:11
 */

namespace App\Services;


use App\Services\Contracts\CurrencyFiles;
use App\File;
use Spatie\ArrayToXml\ArrayToXml;

class xmlHandler implements CurrencyFiles
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

//        $xml = new \SimpleXMLElement('<root/>');
//        array_walk_recursive($newData, array ($xml, 'addChild'));
//        dd($xml->asXML());

        $xmlData = new \SimpleXMLElement('<?xml version="1.0"?><CURRENCIES></CURRENCIES>');

        // function call to convert array to xml
        $this->arrayToXml($newData, $xmlData);

        // saving generated xml file;
        $result = $xmlData->asXML('text.xml');

//        $xml = new \XMLWriter();
//        $xml->openMemory();
//        $xml->startDocument('1.1', 'abc-1111-2', 'yes');
//        $xml->startElement('CURRENCIES');
//
//        foreach ($newData as $key => $value) {
//            if (is_array($value)) {
//                if (is_numeric($key)) {
//                    $xml->startElement('CURRENCIES');
//                }
//            }
//        }

//        $xml = ArrayToXml::convert($newData, 'CURRENCIES');

        // file name
//        $newFileName = 'currency_' . date('Y_m_d_H_i_s') . '.xml';
//
//        // full file name
//        $newFilePath = config('app.files_path_new') . $newFileName;
//
//        file_put_contents($newFilePath, $xml);
//
//        return $newFileName;
    }

    private function arrayToXml($data, &$xmlData) {
        foreach( $data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'item' . $key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) {
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}