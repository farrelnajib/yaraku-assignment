<?php
namespace App\Http\DTO\Responses;

use \Illuminate\Http\Response as HTTPResponse;

class ExportResponse
{
    public string $data;
    public string $fileName;
    public string $contentType;

    public function __construct(string $data, string $fileName, string $contentType)
    {
        $this->data = $data;
        $this->fileName = $fileName;
        $this->contentType = $contentType;
    }

    public function toResponse(): HTTPResponse
    {
        return new HTTPResponse($this->data, 200, [
            "Content-Type" => $this->contentType,
            "Content-Disposition" => "attachment; filename=\"{$this->fileName}\"",
        ]);
    }
}
