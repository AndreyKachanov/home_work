<?php

namespace App\Http\Controllers;

use function Faker\Provider\pt_BR\check_digit;
use Illuminate\Http\Request;
use App;


class DefaultController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexAction()
    {
        return view('index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function uploadAction(Request $request)
    {
        $originalFile = $request->file('file');
        $ext = ['json', 'xml', 'csv'];

        if ($originalFile && in_array($originalFile->getClientOriginalExtension(), $ext)) {


            // get file handler
            $fileHandler = App::makeWith('App\Contracts\CurrencyFiles', [
                'file' => $originalFile
            ]);

            // save user file to database and in directory
            $fileHandler->saveCurrentFile();

            // check sctucture uploaded file
            if ($fileHandler->checkStructure() === false) {
                return response()->json(['structure' => 'fail']);
            }

            // get data from user file
            $uploadedData = $fileHandler->getCurrentData();

            // updated user data and create new file
            $updatedFile = $fileHandler->updateData();

            return view('jsonTable', [
                'uploadedData' => $uploadedData,
                'updatedFile' => $updatedFile
            ]);

        } else {
            return response()->json([]);
        }

    }
}
