<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\S3Helper;
use App\Helpers\CsvHelper;
use \Log;

class EmployeeController extends Controller
{
    /**
     * Upload(csv) all organization chart
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadBulkEmployeesData(Request $request) {
        $file = $request->file('organizationFile');
        $result = S3Helper::upload($file);
        if (!$result) {
            Log::info("File upload failed");
            return \Response()->json(['message' => __('failureMessage.bulkUpload'), 'status' => false]);
        }

        return \Response()->json(['message' => __('successMessage.bulkUpload'), 'status' => true]);
    }

    /**
     * Upload(csv) for specific record's
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadUpdatedEmployeeData(Request $request) {
        $file = $request->file('organizationFile');
        $fileContents = file($file->getPathname());

        // check file is empty
        if (count($fileContents) == 0 || count($fileContents) == 1) {
            return \Response()->json(['message' => __('failureMessage.file_empty'), 'status' => false]);
        }

        $result = CsvHelper::UpdateOrgChart($file);
        if (!$result) {
            return \Response()->json(['message' => __('failureMessage.bulkUpload'), 'status' => false]);
        }

        return \Response()->json(['message' => __('successMessage.bulkUpload'), 'status' => true]);
    }
}


