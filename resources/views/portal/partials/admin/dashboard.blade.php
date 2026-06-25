<div class="space-y-6">
    @if(($stats['pendingUserCount'] ?? 0) > 0)
    <div class="bg-chart-4/10 border border-chart-4/30 rounded-lg p-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <x-icon name="user-check" class="w-5 h-5 text-chart-4"/>
            <p class="text-sm">
                <span class="font-medium">{{ $stats['pendingUserCount'] }} pengguna</span> menunggu verifikasi admin.
            </p>
        </div>
        <button type="button" @click="activeTab='user-verification'; refreshIcons()" class="text-sm px-3 py-1.5 bg-primary text-primary-foreground rounded-lg whitespace-nowrap">
            Lihat Verifikasi
        </button>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-primary/10 rounded-lg mb-4 w-fit"><x-icon name="users" class="w-5 h-5 text-primary" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['totalStudents'] }}</h3>
            <p class="text-sm text-muted-foreground">Total Siswa</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-purple-500/10 rounded-lg mb-4 w-fit"><x-icon name="graduation-cap" class="w-5 h-5 text-purple-600" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['totalTeachers'] }}</h3>
            <p class="text-sm text-muted-foreground">Total Guru</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-green-500/10 rounded-lg mb-4 w-fit"><x-icon name="calendar-check" class="w-5 h-5 text-green-600" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['presentToday'] }}</h3>
            <p class="text-sm text-muted-foreground">Hadir Hari Ini</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-blue-500/10 rounded-lg mb-4 w-fit"><x-icon name="trending-up" class="w-5 h-5 text-blue-600" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['attendanceRate'] }}%</h3>
            <p class="text-sm text-muted-foreground">Tingkat Kehadiran</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-slate-500/10 rounded-lg mb-4 w-fit"><x-icon name="clock" class="w-5 h-5 text-slate-600" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['totalRecords'] }}</h3>
            <p class="text-sm text-muted-foreground">Total Rekaman</p>
        </div>
        <div class="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="p-2.5 bg-orange-500/10 rounded-lg mb-4 w-fit"><x-icon name="file-text" class="w-5 h-5 text-orange-600" /></div>
            <h3 class="text-3xl mb-1 font-display">{{ $stats['totalLeaveRequests'] }}</h3>
            <p class="text-sm text-muted-foreground">Total Izin</p>
        </div>
    </div>

    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h3 class="mb-6 font-display">Kehadiran Mingguan</h3>
        <canvas id="weeklyAttendanceChart" height="120"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('weeklyAttendanceChart');
    if (!el || !window.Chart) return;
    const data = @json($weeklyChart);
    new Chart(el, {
        type: 'bar',
        data: {
            labels: data.map(d => d.day),
            datasets: [
                { label: 'Hadir', data: data.map(d => d.present), backgroundColor: '#1e3a8a', borderRadius: 4 },
                { label: 'Terlambat', data: data.map(d => d.late), backgroundColor: '#f59e0b', borderRadius: 4 },
                { label: 'Tidak Hadir', data: data.map(d => d.absent), backgroundColor: '#dc2626', borderRadius: 4 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true, ticks: { color: '#64748b' } }, x: { ticks: { color: '#64748b' } } },
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
