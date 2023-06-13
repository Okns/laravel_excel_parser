<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportFileExcelRequest;
use App\Jobs\WriteParcedData;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function importFile(ImportFileExcelRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('public/files');
        $name = $file->hashName();

        $parsed = $this->parseFile($path, $name);
        if (!$parsed['status']) abort(400, $parsed['message']);

        return response()->json(['success' => true, 'message' => $parsed['message']]);
    }


    public function parseFile($filePath, $name): array
    {
        if (Storage::exists($filePath)) {
            $filePath = Storage::path($filePath);
            $reader = ReaderEntityFactory::createReaderFromFile($filePath);
            $reader->open($filePath);
            if ($reader->getSheetIterator()->valid()) {

                $key = stristr($name, '.', true);
                foreach ($reader->getSheetIterator() as $sheet) {
                    $rowArray = [];
                    foreach ($sheet->getRowIterator(10) as $key => $row) {
                        $cells = $row->getCells();
                        if ($key == 1) {
                            if ($cells[0]->getValue() == 'id' && $cells[1]->getValue() == 'name' && $cells[2]->getValue() == 'date') {
                                continue;
                            } else {
                                return ['status' => false, 'message' => 'Unexpected rows naming'];
                            }
                        }
                        $rowArray[] = [
                            'rows_id' => $cells[0]->getValue() ?? null,
                            'rows_name' => $cells[1]->getValue() ?? null,
                            'rows_date' => $cells[2]->getValue() ?? null
                        ];
                    }

                    $rowChunks = array_chunk($rowArray, 1000);
                    foreach ($rowChunks as $rowChunk) {
                        dispatch(new WriteParcedData($rowChunk, $key));
                    }
                }
                $reader->close();
                return ['status' => true, 'message' => 'File successfully parsed'];
            }
            return ['status' => false, 'message' => 'File was invalid'];
        }

        return ['status' => false, 'message' => 'Saving file error'];
    }

    public function showImportedRows()
    {
        $resultRaw = DB::table('rows')->orderBy('rows_date')->get();
        $result = [];
        foreach ($resultRaw as $item) {
            $result[Carbon::create($item->rows_date)->format('d-m-Y')][] = [
                'id' => $item->rows_id,
                'name' => $item->rows_name
            ];
        }
        return response()->json(['data' => $result, 'success' => true]);
    }
}
