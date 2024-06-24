<?php

namespace App\Jobs;

use App\Mail\OrderCreatedMail;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendMailUser();
        $this->sendMailAdmins();
    }

    /**
     * Send the order creation email to the user.
     *
     * This method sends an email to the user with the order details and a link to view their order history.
     *
     * @return void
     */
    public function sendMailUser()
    {
        $userEmail = $this->order->user->email;
        $userOrderUrl = url(env('FRONT_END_URL', 'http://localhost:5173') . '/order/history');
        Mail::to($userEmail)->send(new OrderCreatedMail($this->order, 'mails.create-order-user', ($userOrderUrl)));
    }

    /**
     * Send the order creation email to all admins.
     *
     * This method retrieves all admin emails and sends an email to each admin with the order details
     * and a link to view the order details in the admin panel.
     *
     * @return void
     */
    public function sendMailAdmins()
    {
        $adminEmails = Admin::pluck('email')->all();
        $adminOrderUrl = url(env('BACK_END_URL', 'http://127.0.0.1:8000')) . '/admin/orders/' . $this->order->id . '/detail';
        foreach ($adminEmails as $email) {
            Mail::to($email)->send(new OrderCreatedMail($this->order, 'mails.create-order-admin', ($adminOrderUrl)));
        }
    }
}