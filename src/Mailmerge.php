    <?php

namespace Homeful\Mailmerge;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Request;
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
        $download = $request->ResponseType == 'Download' ? true : null;
        $fileName = $request->DocumentName ? $request->DocumentName.'_'.time() : time();

        if ($request->isPublic) {
            Storage::disk('public')->put('mailmerge/temp/'.$fileName.'.docx', $fileDecode);
            $filePath = Storage::disk('public')->path('mailmerge/temp/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, $download);
            Storage::disk('public')->delete('mailmerge/temp/'.$fileName.'.docx');
        } else {
            Storage::put('public/temp/document/'.$fileName.'.docx', $fileDecode);
            $filePath = Storage::path('public/temp/document/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, 'local', $download);
            Storage::delete('public/temp/document/'.$fileName.'.docx');
        }

        return $response;
    }

    public function generateDocument($filePath, array $arrInput, $filename = null, $disk = 'public', $download = null)
    {

        if (! File::exists($filePath)) {
            return response()->json(['Error' => 'File not existing'], 500);
        }
        //create folder if not existing
        if ($disk == 'public') {
            if (! Storage::disk('public')->exists('mailmerge/converted_documents')) {
                Storage::disk('public')->makeDirectory('mailmerge/converted_documents');
                chmod(Storage::disk('public')->path('mailmerge/converted_documents'), 0755);
            }
            if (! Storage::disk('public')->exists('mailmerge/converted_pdf')) {
                Storage::disk('public')->makeDirectory('mailmerge/converted_pdf');
                chmod(Storage::disk('public')->path('mailmerge/converted_pdf'), 0755);
            }
        } elseif ($disk == 'local') {
            if (! Storage::exists('mailmerge/converted_documents')) {
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
                foreach ($value as $subKey => &$subValue) {
                    if (is_null($subValue)) {
                        $subValue = '';
                    }
                }
            } elseif (is_null($value)) {
                $value = '';
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
        if ($disk == 'public') {
            $filePath = Storage::disk('public')->path('mailmerge/converted_documents/'.$filename.'.docx');
            $outputFile = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $outputDir = Storage::disk('public')->path('mailmerge/converted_pdf/');
            $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf");
        } elseif ($disk == 'local') {
            $filePath = Storage::path('mailmerge/converted_documents/'.$filename.'.docx');
            $outputFile = Storage::path('mailmerge/converted_pdf/');
            $outputDir = Storage::path('mailmerge/converted_pdf/');
            $pdfFile = Storage::path("mailmerge/converted_pdf/{$filename}.pdf");
        }
        $templateProcessor->saveAs($filePath);

        $libreOfficePath = env('LIBREOFFICE_PATH', 'C:\Program Files\LibreOffice\program\soffice.exe');
        $filePathEscaped = escapeshellarg($filePath);

        $command = "\"{$libreOfficePath}\" --headless --convert-to pdf:writer_pdf_Export --outdir \"{$outputDir}\" {$filePathEscaped}";
        try {
            exec($command, $outputFile, $return_var);
            // return $filePathEscaped;
            // $pdfFile = storage_path("app/public/converted_pdf/{$filename}_templated.pdf");
            $pdfFile = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf");
            // echo $url;

            if ($disk == 'public') {
                if ($download) {
                    return Storage::disk('public')->download("mailmerge/converted_pdf/{$filename}.pdf", $filename);
                }
                $pdfPath = Storage::disk('public')->path("mailmerge/converted_pdf/{$filename}.pdf");

                return $pdfPath;
            }
            // return Storage::disk('public')->download("mailmerge//converted_pdf//{$filename}.pdf",$filename);
            elseif ($disk == 'local') {
                // $url = Storage::url(
                //     $pdfFile,
                //     now()->addMinutes(5),
                //     [
                //         'ResponseContentType' => 'application/octet-stream',
                //         'ResponseContentDisposition' => "attachment; filename={$filename}_templated.jpg",
                //     ]
                //         );
                if ($download) {
                    return Storage::download("mailmerge/converted_pdf/{$filename}.pdf", $filename);
                }
                $pdfPath = Storage::path("mailmerge/converted_pdf/{$filename}.pdf");

                return $pdfPath;

                // return Storage::download("mailmerge//converted_pdf//{$filename}.pdf",$filename);
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

    public function downloadDocument($filePath, $filename, $disk = 'public')
    {
        if ($disk == 'public') {
            return Storage::disk('public')->download($filePath, $filename);
        } elseif ($disk == 'local') {
            return Storage::download($filePath, $filename);
        }
    }

    public function getDocumentGithub(string $owner, string $repo, string $filepath)
    {
        $token = env('GIT_TOKEN');
        $getURL = 'https://api.github.com/repos/'.$owner.'/'.$repo.'/contents/'.$filepath;
        $headers = [
            'Authorization' => $token,
            'Accept' => 'application/vnd.github.v3+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ];
        $client = new Client;
        $request = new GuzzleRequest('GET', $getURL, $headers);
        $res = $client->sendAsync($request)->wait();
        $responseBody = json_decode($res->getBody()); //->getContents();

        return $responseBody->download_url; //->content;
    }

    public function requestDocumentGithub(Request $request)
    {
        $fileDecode = $this->getDocumentGithub($request->owner, $request->repo, $request->filepath);
        $arrInput = $request->bodyInput; //json_decode($request->bodyInput, true);
        $download = $request->ResponseType == 'Download' ? true : null;
        $fileName = $request->DocumentName ? $request->DocumentName.'_'.time() : time();

        if ($request->isPublic) {
            Storage::disk('public')->put('mailmerge/temp/'.$fileName.'.docx', Utils::streamFor(fopen($fileDecode, 'r')));
            $filePath = Storage::disk('public')->path('mailmerge/temp/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, $download);
            Storage::disk('public')->delete('mailmerge/temp/'.$fileName.'.docx');
        } else {
            Storage::put('public/temp/document/'.$fileName.'.docx', Utils::streamFor(fopen($fileDecode, 'r')));
            $filePath = Storage::path('public/temp/document/'.$fileName.'.docx');
            $response = $this->generateDocument($filePath, $arrInput, $fileName, 'local', $download);
            Storage::delete('public/temp/document/'.$fileName.'.docx');
        }

        return $response;
    }
}
