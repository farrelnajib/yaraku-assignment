<?php
namespace App\Http\DTO;

class Book
{
    public int $id;
    public string $title;
    public string $author;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(int $id, string $title, string $author, string $createdAt, string $updatedAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromModel(\App\Book $data): self {
        return new self(
            $data->id,
            $data->title,
            $data->author,
            $data->created_at,
            $data->updated_at
        );
    }
}
