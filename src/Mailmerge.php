<?php

namespace Homeful\Mailmerge;

use Illuminate\Http\Request;
// use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpWord\TemplateProcessor;

class Mailmerge
{
    public function requestDocument(Request $request)
    {
        $fileDecode = base64_decode($request->DocumentBase64);
        $arrInput = json_decode($request->BodyInput, true);
<<<<<<< HEAD
        $download = $request->ResponseType=="Download"?true:null;
        $fileName = $request->DocumentName?$request->DocumentName."_".time():time();

        if($request->isPublic)
        {
            Storage::disk('public')->put('mailmerge/temp/' .$fileName.'.docx', $fileDecode);
            $filePath = Storage::disk('public')->path('mailmerge/temp/' .$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, $download);
            Storage::disk('public')->delete('mailmerge/temp/' .$fileName.'.docx');
        }
        else
        {
        Storage::put('public/temp/document/' .$fileName.'.docx', $fileDecode);
        $filePath = Storage::path('public/temp/document/' .$fileName.'.docx');
        $response = $this->generateDocument($filePath, $arrInput, $fileName, "local", $download);
        Storage::delete('public/temp/document/' .$fileName.'.docx');
=======
        $fileName = $request->DocumentName ? $request->DocumentName.'_'.time() : time();

        if ($request->isPublic) {
            Storage::disk('public')->put('mailmerge/temp/'.$fileName.'.docx', $fileDecode);
            $filePath = Storage::disk('public')->path('mailmerge/temp/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName);
            Storage::disk('public')->delete('mailmerge/temp/'.$fileName.'.docx');
        } else {
            Storage::put('public/temp/document/'.$fileName.'.docx', $fileDecode);
            $filePath = Storage::path('public/temp/document/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, 'local');
            Storage::delete('public/temp/document/'.$fileName.'.docx');
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
        }

        return $response;
    }

<<<<<<< HEAD
    public function generateDocument($filePath,array $arrInput ,$filename = null, $disk = "public",$download = null){

        if (!File::exists($filePath)) {
            return response()->json(['Error' => 'File not existing'], 500);
        }
        //create folder if not existing
        if($disk == "public")
        {
            if (!Storage::disk('public')->exists('mailmerge/converted_documents')) {
=======
    public function generateDocument($filePath, array $arrInput, $filename = null, $disk = 'public')
    {
        if (! File::exists($filePath)) {
            return response()->json(['Error' => 'File not existing'], 500);
        }
        //create folder if not existing
        if ($disk = 'public') {
            if (! Storage::disk('public')->exists('mailmerge/converted_documents')) {
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
                Storage::disk('public')->makeDirectory('mailmerge/converted_documents');
                chmod(Storage::disk('public')->path('mailmerge/converted_documents'), 0755);
            }
            if (! Storage::disk('public')->exists('mailmerge/converted_pdf')) {
                Storage::disk('public')->makeDirectory('mailmerge/converted_pdf');
                chmod(Storage::disk('public')->path('mailmerge/converted_pdf'), 0755);
<<<<<<< HEAD
            }  
        }
        elseif($disk == "local")
        {
            if (!Storage::exists('mailmerge/converted_documents')) {
=======
            }
        } elseif ($disk = 'local') {
            if (! Storage::exists('mailmerge/converted_documents')) {
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
                Storage::makeDirectory('mailmerge/converted_documents');
                chmod(Storage::path('mailmerge/converted_documents'), 0755);
            }
            if (! Storage::exists('mailmerge/converted_pdf')) {
                Storage::makeDirectory('mailmerge/converted_pdf');
                chmod(Storage::path('mailmerge/converted_pdf'), 0755);
            }
        }

        $templateProcessor = new TemplateProcessor($filePath);
        $data = $arrInput;
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                // Recursively process arrays
                foreach ($value as $subKey => &$subValue) {
                    if (is_null($subValue)) {
                        $subValue = ''; // Set null values to empty strings in nested arrays
                    }
                }
            } elseif (is_null($value)) {
                $value = ''; // Set null values to empty strings
            }
        }

        foreach ($data as $key => $value) {

            // echo $value;
            // echo htmlspecialchars($value??'') ;
            $templateProcessor->setValue($key, htmlspecialchars($value ?? ''));
        }
        //set image
        //        $imagePath = storage_path('app/public/test_image.png');
        //        $templateProcessor->setImageValue('image', array('path' => $imagePath, 'width' => 100, 'height' => 100, 'ratio' => false));

        $filename = $filename ? $filename : preg_replace('/[^A-Za-z0-9_\-]/', '', now()->format('Ymd_His'));

        // $filePath = Storage::path('app/public/converted_documents/' . $filename . '_templated.docx');
<<<<<<< HEAD
        if($disk == "public"){
            $filePath = Storage::disk('public')->path('mailmerge/converted_documents/' . $filename . '.docx');
            $outputFile = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $outputDir = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf"); 
        }
        elseif($disk == "local")
        {
            $filePath = Storage::path('mailmerge/converted_documents/' . $filename . '.docx'); 
=======
        if ($disk = 'public') {
            $filePath = Storage::disk('public')->path('mailmerge/converted_documents/'.$filename.'.docx');
            $outputFile = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $outputDir = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf");
        } elseif ($disk = 'local') {
            $filePath = Storage::path('mailmerge/converted_documents/'.$filename.'.docx');
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
            $outputFile = Storage::path('mailmerge/converted_pdf/');
            $outputDir = Storage::path('mailmerge/converted_pdf/');
            $pdfFile = Storage::path("mailmerge/converted_pdf/{$filename}.pdf");
        }
        $templateProcessor->saveAs($filePath);
<<<<<<< HEAD
        
=======

        // $outputFile = storage_path('app/public/converted_pdf/');
        // $libreOfficePath = env('LIBREOFFICE_PATH', 'C:\Program Files\LibreOffice\program\soffice.exe');
        // $outputDir = storage_path('app/public/converted_pdf/');
        // $outputFile = Storage::disk('public')->path('mailmerge/converted_pdf/');s
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
        $libreOfficePath = env('LIBREOFFICE_PATH', 'C:\Program Files\LibreOffice\program\soffice.exe');
        $filePathEscaped = escapeshellarg($filePath);

        $command = "\"{$libreOfficePath}\" --headless --convert-to pdf:writer_pdf_Export --outdir \"{$outputDir}\" {$filePathEscaped}";
<<<<<<< HEAD
        try{
            exec($command, $outputFile, $return_var);
            // return $filePathEscaped;
            // $pdfFile = storage_path("app/public/converted_pdf/{$filename}_templated.pdf");
            $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf"); 
            // echo $url;

            if($disk == "public")
            {
                if($download)
                {
                    return Storage::disk('public')->download("mailmerge/converted_pdf/{$filename}.pdf",$filename);    
                }
                $pdfPath = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf"); 
                return $pdfPath;
            }
            // return Storage::disk('public')->download("mailmerge//converted_pdf//{$filename}.pdf",$filename);
            elseif($disk == "local")
            {
                // $url = Storage::url(
                //     $pdfFile,
                //     now()->addMinutes(5),
                //     [
                //         'ResponseContentType' => 'application/octet-stream',
                //         'ResponseContentDisposition' => "attachment; filename={$filename}_templated.jpg",
                //     ]
                //         );
                if($download)
                {
                    return Storage::download("mailmerge/converted_pdf/{$filename}.pdf",$filename);    
                }
                $pdfPath = Storage::path("mailmerge/converted_pdf/{$filename}.pdf"); 
     
                return $pdfPath;

            // return Storage::download("mailmerge//converted_pdf//{$filename}.pdf",$filename);   
=======
        //dd($command);
        // $command = env('LIBREOFFICE_PATH')." --headless --convert-to pdf:writer_pdf_Export --outdir '".storage_path('app/public/converted_pdf/'). "' " . escapeshellarg($filePath);
        try {
            exec($command, $outputFile, $return_var);
            // return $filePathEscaped;
            // $pdfFile = storage_path("app/public/converted_pdf/{$filename}_templated.pdf");
            // $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf");

            // $url = Storage::url(
            //     $pdfFile,
            //     now()->addMinutes(5),
            //     [
            //         'ResponseContentType' => 'application/octet-stream',
            //         'ResponseContentDisposition' => "attachment; filename={$filename}_templated.jpg",
            //     ]
            // );
            // echo $url;
            if ($disk = 'public') {
                return Storage::disk('public')->download("mailmerge//converted_pdf//{$filename}.pdf", $filename);
            } elseif ($disk = 'local') {
                return Storage::download("mailmerge//converted_pdf//{$filename}.pdf", $filename);
>>>>>>> e738071cf500d3e4fcf870b183b00b4e465de4eb
            }
            // return $url;
        } catch (e) {
            return response()->json(['error' => 'An error occurred during the file conversion'], 500);
        }

        // dd($pdfFile);
        // if (file_exists($pdfFile)) {
        //     return $isView? response()->file($pdfFile, [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => 'inline; filename="' . basename($pdfFile) . '"'
        //     ]):response()->download($pdfFile);
        // } else {
        //     return response()->json(['error' => 'An error occurred during the file conversion'], 500);
        // }
    }
    public function downloadDocument($filePath,$filename, $disk = "public"){
        if($disk == "public")
        return Storage::disk('public')->download($filePath,$filename);
        elseif($disk == "local")
        {
        return Storage::download($filePath,$filename);   
        }
    }
}
