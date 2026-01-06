<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?License $license;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, ?License $license = null)
    {
        $this->order = $order;
        $this->license = $license;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Payment Successful - ' . ($this->order->product->name ?? 'Your Order'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-completed',
            with: [
                'order' => $this->order,
                'license' => $this->license,
                'product' => $this->order->product,
                'downloadUrl' => $this->license ? route('dashboard.orders.show', $this->order) : null,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach product file if exists
        if ($this->order->product && $this->order->product->file_path) {
            $filePath = $this->order->product->file_path;
            
            if (Storage::disk('public')->exists($filePath)) {
                $attachments[] = Attachment::fromStorageDisk('public', $filePath)
                    ->as($this->order->product->file_name ?? basename($filePath))
                    ->withMime(Storage::disk('public')->mimeType($filePath));
            }
        }

        return $attachments;
    }
}
