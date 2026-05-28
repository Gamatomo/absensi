<x-layouts.app title="Dashboard">
    <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.stat-card label="Total Siswa" :value="$studentCount" />
        <x-ui.stat-card label="Total Guru" :value="$teacherCount" />
        <x-ui.stat-card label="Absensi Hari Ini" :value="$todayAttendance" />
        <x-ui.stat-card label="Terlambat" :value="$lateCount" />
    </div>

    <div class="mt-6">
        <x-ui.card title="Sistem RFID + Face Verification" subtitle="Raspberry Pi handles scanner and camera; Laravel handles records and reporting.">
            <div class="text-sm text-muted-foreground space-y-1">
                <p>- Device sends raw attendance events through API.</p>
                <p>- Laravel stores immutable event log and normalized attendance records.</p>
                <p>- Admin can review mismatched face verifications manually.</p>
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
