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
     * check structure json file
     *
     * @return bool
     */
    public function checkStructure()
    {
        if ($this->getCurrentData() === false) {
            return false;
        }
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

    /**Get current data
     * @return array
     */
    public function getCurrentData()
    {
        $file = config('app.files_path') . $this->fileName;
        $xmlFile = file_get_contents($file);

        try {
            $ob = simplexml_load_string($xmlFile);
        } catch (\Exception $e) {
            return false;
        }
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

        // file name
        $newFileName = 'currency_' . date('Y_m_d_H_i_s') . '.xml';

        // full file name
        $newFilePath = config('app.files_path_new') . $newFileName;

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'iso-8859-8', 'yes');
        $xmlWriter->startElement('CURRENCIES');
        $this->arrayToXml($xmlWriter, $newData);
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        $xml = $xmlWriter->outputMemory();

        $file = fopen($newFilePath,'w');
        fputs($file, $xml);
        fclose($file);

        return $newFileName;
    }

    /**
     * SimpleXml method
     * @return string
     */
//    public function updateData()
//    {
//        date_default_timezone_set('Europe/Kiev');
//        $data = $this->getCurrentData();
//
//        $newData = CurrencyService::updateCurrencyRate($data, $caseLower = true);
//        // file name
//        $newFileName = 'currency_' . date('Y_m_d_H_i_s') . '.xml';
//
//        // full file name
//        $newFilePath = config('app.files_path_new') . $newFileName;
/*        $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><CURRENCIES></CURRENCIES>');*/
//        $this->arrayToXmlSimple($newData, $xml_data);
//        $xml_data->asXML($newFilePath);
//        return $newFileName;
//    }

    /**
     * Converts an array to XML
     *
     * @param \XMLWriter $xml
     * @param $array
     * @param string $rootNodeName
     */
    private function arrayToXml(\XMLWriter $xml, $array, $rootNodeName = 'CURRENCIES')
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $xml->startElement(strtoupper($rootNodeName));
                    $this->arrayToXml($xml, $value, $rootNodeName);
                    $xml->endElement();
                } else {
                    $this->arrayToXml($xml, $value, $key);
                }
            } else {
                $xml->writeElement(strtoupper($key), $value);
            }
        }
    }

    /**
     * @param $data
     * @param $xml_data
     */
    private function arrayToXmlSimple( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'item'.$key;
            }
            if( is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                $this->arrayToXmlSimple($value, $subnode);
            }
            else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
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