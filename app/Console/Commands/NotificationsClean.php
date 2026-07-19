<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('notifications:clean')]
#[Description('Hapus notifikasi lebih dari 30 hari')]
class NotificationsClean extends Command
{
    public function handle()
    {
        $deleted = Notification::olderThanDays(30)->delete();

        $this->info("Berhasil menghapus {$deleted} notifikasi lama.");
    }
}
