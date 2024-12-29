<?php
namespace App\Http\DTO;

use App\ExportJob;

class Export
{
    public int $id;
    public string $status;
    public string $type;
    public ?string $downloadUrl;
    public array $fields;

    public function __construct(int $id, string $status, string $type, ?string $downloadUrl, array $fields){
        $this->id = $id;
        $this->status = $status;
        $this->type = $type;
        $this->downloadUrl = $downloadUrl;
        $this->fields = $fields;
    }

    public static function fromModel(ExportJob $job): self
    {
        return new self(
            $job->id,
            $job->status,
            $job->type,
            $job->download_url,
            $job->fields,
        );
    }
}
