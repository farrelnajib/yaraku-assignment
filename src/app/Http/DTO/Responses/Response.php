<?php
namespace App\Http\DTO\Responses;

class Response
{
    public object $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }
}