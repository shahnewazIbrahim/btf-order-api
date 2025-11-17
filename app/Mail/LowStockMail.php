<?php

namespace App\Mail;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inventory $inventory)
    {
    }

    public function build()
    {
        return $this->subject('Low Stock Alert: '.$this->inventory->variant->sku)
            ->markdown('emails.low_stock');
    }
}
