<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesananResource extends JsonResource
{
    // Properti untuk menampung status dan pesan
    public $status;
    public $message;

    /**
     * Konstruktor untuk menerima status dan pesan.
     */
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Jika $this->resource adalah koleksi (misal dari paginate)
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'success' => $this->status,
                'message' => $this->message,
                'data'    => $this->resource,
            ];
        }

        // Jika $this->resource adalah model tunggal
        return [
            'success' => $this->status,
            'message' => $this->message,
            // 'data' akan berisi data dari model Pesanan
            // Kita bisa kustomisasi ini jika perlu
            'data'    => $this->resource,
        ];
    }
}
