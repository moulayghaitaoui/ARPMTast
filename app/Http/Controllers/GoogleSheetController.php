<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Http\Request;

class GoogleSheetController extends Controller
{
    public function generateAndUpload()
    {
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $row = [];
            $cumulativeSum = 0;
            for ($j = 0; $j < 52; $j++) {
                $randomValue = mt_rand() / mt_getrandmax();
                $cumulativeSum += $randomValue;
                $row[] = $cumulativeSum;
            }
            array_unshift($row, "Person " . ($i + 1));
            $data[] = $row;
        }

        // إعداد Google Client
        $client = new Client();
        $client->setAuthConfig(storage_path('google_sheets_credentials.json')); // تأكد من أن المسار صحيح
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        // تهيئة خدمة Google Sheets
        $service = new Sheets($client);
        $spreadsheet = $service->spreadsheets->create([
            'properties' => ['title' => 'Cumulative Sums Chart']
        ]);

        $spreadsheetId = $spreadsheet->spreadsheetId;
        $range = 'Sheet1!A1';
        $body = new ValueRange([
            'values' => array_merge([
                array_merge(['Individual'], range(1, 52)) // إضافة العناوين
            ], $data)
        ]);

        // تحديث بيانات Google Sheet
        $service->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );

        // إرجاع رابط Google Sheet
        $sheetUrl = "https://docs.google.com/spreadsheets/d/" . $spreadsheetId;
        return response()->json(["message" => "Google Sheet created!", "url" => $sheetUrl]);
    }
}
