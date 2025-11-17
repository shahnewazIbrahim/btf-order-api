<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportProductsFromCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $path,
        public int $vendorId
    ) {
    }

    public function handle(): void
    {
        $fullPath = Storage::path($this->path);

        if (! file_exists($fullPath)) {
            return;
        }

        if (($handle = fopen($fullPath, 'r')) === false) {
            return;
        }

        // প্রথম লাইন header ধরলাম
        $header = fgetcsv($handle, 0, ',');

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $data = array_combine($header, $row);

            if (empty($data['name']) || empty($data['base_price'])) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'description' => $data['description'] ?? null,
                    'base_price'  => (float) $data['base_price'],
                    'is_active'   => !empty($data['is_active']),
                    'user_id'     => $this->vendorId,
                ]
            );
        }

        fclose($handle);

        // ইচ্ছা করলে শেষে ফাইল ডিলিট করতে পারো
        Storage::delete($this->path);
    }
}
