<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Helpers\S3Helper;
use \Log;

class CsvHelper
{
    // This function will execute actions steps by steps
    // 1 - Download existing organization chart csv
    // 2 - Process it
    // 3 - Process uploaded csv
    // 4 - Update the latest changes from uploaded csv into existing organization chart csv
    // 5 - Create new organization chart csv
    // 6 - Upload it to S3
    public static function updateOrgChart($file)
    {
        $result = true;
        $masterFile = self::downloadOrgChart();
        $allOrgData = self::processMasterFileData($masterFile);
        $latestData = self::processChildFileData($file, $allOrgData);
        $updatedMasterData = self::compiledCSVs($allOrgData, $latestData);
        $newOrgChart = self::createNewOrgCSV($updatedMasterData);

        try {
            S3Helper::init()->putObject(['Bucket' => config('app.s3_bucket'), 'Key' => 'org.csv', 'SourceFile' => $newOrgChart]);
        } catch(\Exception $e) {
            Log::info("File uploading failed. ".$e->getMessage());
            $result = false;
        }

        return $result;
    }

    // Download existing organization chart csv
    public static function downloadOrgChart()
    {
        try {
            return S3Helper::download();
        } catch(\Exception $e) {
            Log::info("downloadOrgChart ".$e->getMessage());
            \Response()->json(['message' => __('failureMessage.master_file_download'), 'status' => false]);
            die();
        }
    }

    // Process existing organization file data
    public static function processMasterFileData($fileContent)
    {
        $convertToArray = explode("\n", $fileContent);
        $allOrganizationEmployee = [];
        $allOrganizationEmployee['data'] = [];
        foreach ($convertToArray as $key => $line) {
            $row = str_getcsv($line);
            if ($key != 0) {
                $employee = [
                    'job_id' => $row[0],
                    'job_title' => $row[1],
                    'employee_name' => $row[2],
                    'employee_id' => $row[3],
                    'email' => $row[4],
                    'reporting_to_job_id' => $row[5],
                    'reporting_to_name' => $row[6],
                    'role_priority' => $row[7],
                    'job_level' => $row[8],
                    'is_root' => $row[9],
                ];
                $allOrganizationEmployee['data'][$row[3]] = $employee;
            } else {
                $allOrganizationEmployee['header'] = $row;
            }
        }
        return $allOrganizationEmployee;
    }

    // Process updated csv file data
    public static function processChildFileData($file, $masterFile)
    {
        $child = file($file->getPathname());
        $data = [];
        $data['data'] = [];
        foreach ($child as $key => $line) {
            $row = str_getcsv($line);
            if ($key != 0) {
                $employee = [
                    'job_id' => $row[0],
                    'job_title' => $row[1],
                    'employee_name' => $row[2],
                    'employee_id' => $row[3],
                    'email' => $row[4],
                    'reporting_to_job_id' => $row[5],
                    'reporting_to_name' => $row[6],
                    'role_priority' => $row[7],
                    'job_level' => $row[8],
                    'is_root' => $row[9],
                ];
                array_push($data['data'], $employee);
            } else {
                $data['header'] = $row;
            }
        }
        return $data;
    }

    // compileted both csv files data
    public static function compiledCSVs($allOrgData, $latestData)
    {
        //dd($allOrgData);
        foreach ($latestData['data'] as $key => $data) {
            if (isset($allOrgData['data'][$data['employee_id']])
                && $allOrgData['data'][$data['employee_id']]['employee_id'] == $data['employee_id']
            ) {
                $allOrgData['data'][$data['employee_id']] = $data;
            }
        }

        return $allOrgData;
    }

    // create new csv fie with latest changes
    public static function createNewOrgCSV($updatedMasterData)
    {
        try {
            $path = storage_path('app/public/org.csv');
            $file = fopen($path, 'w');
            $columns = [];

            foreach($updatedMasterData['header'] as $column) {
                array_push($columns, $column);
            }
            
            fputcsv($file, $columns);

            foreach($updatedMasterData['data'] as $data) {
                fputcsv($file, $data);
            }

            fclose($file);
            return $path;
        } catch(\Exception $e) {
            Log::info("createNewOrgCSV. ".$e->getMessage());
            \Response()->json(['message' => __('failureMessage.create_new_csv'), 'status' => false]);
            die();
        }
    }
}
