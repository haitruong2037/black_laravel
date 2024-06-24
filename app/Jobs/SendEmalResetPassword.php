<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmalResetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $actionURL;
    protected $email;

    /**
     * Create a new job instance.
     */
    public function __construct($actionURL, $email)
    {
        $this->actionURL = $actionURL;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new ResetPasswordMail($this->actionURL));
        } catch (\Exception $e) {
            \Log::error('Error sending email reset password: ' . $e->getMessage());
        }
    }
}
