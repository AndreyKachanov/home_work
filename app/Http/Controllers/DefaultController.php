<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
        $file = $request->file('file');

        $ext = ['json', 'xml', 'csv'];

        if ($file && in_array($file->getClientOriginalExtension(), $ext)) {
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = storage_path() . '/json/';
            $file->move($path, $fileName);

            $json = json_decode(file_get_contents($path . $fileName), true);


            return view('jsonTable', [
                'data' => $json
            ]);

        } else {
            return response()->json([]);
        }

    }
}
