<x-layouts.portal :title="'Sistem Presensi'">
<div
    class="min-h-screen bg-background"
    x-data="{
        userRole: @js($userRole),
        activeTab: 'dashboard',
        studentTab: 'dashboard',
        teacherTab: 'dashboard',
        parentTab: 'dashboard',
        roles: ['admin', 'student', 'teacher', 'parent'],
        roleLabels: @js($roleLabels),
        roleSubtitles: @js($roleSubtitles),
        cycleRole() {
            const idx = this.roles.indexOf(this.userRole);
            const next = this.roles[(idx + 1) % this.roles.length];
            window.location.href = '{{ route('dashboard') }}?role=' + next;
        },
        refreshIcons() { $nextTick(() => window.lucide && window.lucide.createIcons()); }
    }"
    x-init="refreshIcons()"
>
    <header class="border-b border-border bg-card shadow-sm">
        <div class="container mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-primary p-3 rounded-lg">
                        <x-icon name="calendar-check" class="w-8 h-8 text-primary-foreground" />
                    </div>
                    <div>
                        <h1 class="text-2xl tracking-tight font-display">Sistem Presensi</h1>
                        <p class="text-sm text-muted-foreground" x-text="roleSubtitles[userRole]"></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-3 bg-secondary px-4 py-2 rounded-lg border border-border">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-background rounded-md border border-border">
                            <x-icon name="scan-face" class="w-4 h-4 text-primary" />
                            <span class="text-sm text-foreground">Pengenalan Wajah</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-background rounded-md border border-border">
                            <x-icon name="credit-card" class="w-4 h-4 text-accent" />
                            <span class="text-sm text-foreground">Akses Kartu</span>
                        </div>
                    </div>
                    @if($userRole === 'admin')
                    {{-- <button
                        type="button"
                        @click="cycleRole()"
                        class="flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary rounded-lg border border-primary/30 transition-all"
                    >
                        <x-icon name="user" class="w-4 h-4" />
                        <span class="text-sm" x-text="roleLabels[userRole]"></span>
                    </button> --}}
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm px-3 py-2 rounded-lg border border-border hover:bg-secondary">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-6 py-8">
        {{-- STUDENT PORTAL --}}
        <div x-show="userRole === 'student'">
                <div class="mb-8">
                    <div class="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
                        @foreach(['dashboard' => ['home', 'Beranda'], 'recap' => ['clipboard-list', 'Rekap Absensi'], 'leave' => ['file-text', 'Pengajuan Izin'], 'profile' => ['user', 'Profil']] as $key => [$icon, $label])
                        <button type="button" @click="studentTab='{{ $key }}'; refreshIcons()" :class="studentTab === '{{ $key }}' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-secondary/80'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all">
                            <x-icon name="{{ $icon }}" class="w-4 h-4" />
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div x-show="studentTab === 'dashboard'">@include('portal.partials.student.dashboard')</div>
                <div x-show="studentTab === 'recap'" x-cloak>@include('portal.partials.student.recap')</div>
                <div x-show="studentTab === 'leave'" x-cloak>@include('portal.partials.student.leave')</div>
                <div x-show="studentTab === 'profile'" x-cloak>@include('portal.partials.student.profile')</div>
        </div>

        {{-- TEACHER PORTAL --}}
        <div x-show="userRole === 'teacher'" x-cloak>
                <div class="mb-8">
                    <div class="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
                        @foreach(['dashboard' => ['home', 'Beranda'], 'recap' => ['clipboard-list', 'Rekap Absensi'], 'leave' => ['file-text', 'Pengajuan Cuti'], 'profile' => ['user', 'Profil']] as $key => [$icon, $label])
                        <button type="button" @click="teacherTab='{{ $key }}'; refreshIcons()" :class="teacherTab === '{{ $key }}' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-secondary/80'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all">
                            <x-icon name="{{ $icon }}" class="w-4 h-4" />
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div x-show="teacherTab === 'dashboard'">@include('portal.partials.teacher.dashboard')</div>
                <div x-show="teacherTab === 'recap'" x-cloak>@include('portal.partials.teacher.recap')</div>
                <div x-show="teacherTab === 'leave'" x-cloak>@include('portal.partials.teacher.leave')</div>
                <div x-show="teacherTab === 'profile'" x-cloak>@include('portal.partials.teacher.profile')</div>
        </div>

        {{-- PARENT PORTAL --}}
        <div x-show="userRole === 'parent'" x-cloak>
                <div class="mb-8">
                    <div class="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
                        <button type="button" @click="parentTab='dashboard'; refreshIcons()" :class="parentTab === 'dashboard' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-secondary/80'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all">
                            <x-icon name="home" class="w-4 h-4" /> Beranda
                        </button>
                        {{-- <button type="button" @click="parentTab='schedule'; refreshIcons()" :class="parentTab === 'schedule' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-secondary/80'" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all">
                            <x-icon name="clock" class="w-4 h-4" /> Jadwal Pelajaran
                        </button> --}}
                    </div>
                </div>
                <div x-show="parentTab === 'dashboard'">@include('portal.partials.parent.dashboard')</div>
                <div x-show="parentTab === 'schedule'" x-cloak>@include('portal.partials.parent.schedule')</div>
        </div>

        {{-- ADMIN PORTAL --}}
        <div x-show="userRole === 'admin'" x-cloak>
                <div class="mb-8 overflow-x-auto">
                    <div class="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border whitespace-nowrap">
                        @foreach([
                            'dashboard' => ['bar-chart-3', 'Beranda'],
                            'user-verification' => ['user-check', 'Verifikasi Pengguna'],
                            'students' => ['users', 'Data Siswa'],
                            'teachers' => ['graduation-cap', 'Data Guru'],
                            'classes' => ['book-open', 'Data Kelas'],
                            'parents' => ['heart', 'Data Orang Tua'],
                            // 'schedule' => ['clock', 'Jadwal Pelajaran'],
                            'recap' => ['file-spreadsheet', 'Rekap Absensi'],
                            'leave-requests' => ['file-text', 'Kelola Izin/Cuti'],
                        ] as $tab => [$icon, $label])
                        <button type="button" @click="activeTab='{{ $tab }}'; refreshIcons()" :class="activeTab === '{{ $tab }}' ? 'bg-primary text-primary-foreground shadow-sm' : ''" class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all">
                            <x-icon name="{{ $icon }}" class="w-4 h-4" />
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div x-show="activeTab === 'dashboard'">@include('portal.partials.admin.dashboard')</div>
                <div x-show="activeTab === 'user-verification'" x-cloak>@include('portal.partials.admin.user-verification')</div>
                <div x-show="activeTab === 'students'" x-cloak>@include('portal.partials.admin.students')</div>
                <div x-show="activeTab === 'teachers'" x-cloak>@include('portal.partials.admin.teachers')</div>
                <div x-show="activeTab === 'classes'" x-cloak>@include('portal.partials.admin.classes')</div>
                <div x-show="activeTab === 'parents'" x-cloak>@include('portal.partials.admin.parents')</div>
                <div x-show="activeTab === 'schedule'" x-cloak>@include('portal.partials.admin.schedule')</div>
                <div x-show="activeTab === 'recap'" x-cloak>@include('portal.partials.admin.recap')</div>
                <div x-show="activeTab === 'leave-requests'" x-cloak>@include('portal.partials.admin.leave-requests')</div>
        </div>
    </main>
</div>

<style>[x-cloak]{display:none!important}</style>
</x-layouts.portal>
