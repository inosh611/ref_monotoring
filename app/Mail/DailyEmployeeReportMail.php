<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyEmployeeReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $date,
        public string $pdfPath
    ) {}

    public function build()
    {
        return $this->subject("Daily Report - {$this->employeeName} ({$this->date})")
            ->view('emails.daily_employee_report', [
                'employeeName' => $this->employeeName,
                'date' => $this->date,
            ])
            ->attach($this->pdfPath, [
                'as' => "Daily_Report_{$this->date}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
