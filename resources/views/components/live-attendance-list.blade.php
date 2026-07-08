<div 
    x-data="liveAttendanceList()" 
    class="bg-card/90 backdrop-blur-md border border-border rounded-2xl shadow-xl shadow-primary/5 h-full flex flex-col overflow-hidden"
>
    <div class="px-5 py-4 border-b border-border bg-secondary/30">
        <h3 class="font-display font-medium text-lg flex items-center gap-2">
            <i data-lucide="users" class="w-5 h-5 text-primary"></i>
            Presensi Hari Ini
        </h3>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-border">
        <button 
            @click="activeTab = 'students'" 
            class="flex-1 py-3 text-sm font-medium transition-colors border-b-2"
            :class="activeTab === 'students' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-secondary/20'"
        >
            Murid (<span x-text="students.length"></span>)
        </button>
        <button 
            @click="activeTab = 'teachers'" 
            class="flex-1 py-3 text-sm font-medium transition-colors border-b-2"
            :class="activeTab === 'teachers' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground hover:bg-secondary/20'"
        >
            Guru (<span x-text="teachers.length"></span>)
        </button>
    </div>

    {{-- List Content --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-3 relative">
        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-card/50 backdrop-blur-sm z-10">
            <svg class="animate-spin w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>

        <template x-for="item in (activeTab === 'students' ? students : teachers)" :key="item.name + item.time">
            <div class="flex items-center justify-between p-3 rounded-xl bg-secondary/20 border border-border/50 hover:bg-secondary/40 transition-colors animate-in slide-in-from-right-4 fade-in duration-300">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm uppercase" x-text="item.name.substring(0, 2)">
                    </div>
                    <div>
                        <p class="font-medium text-sm text-foreground line-clamp-1" x-text="item.name"></p>
                        <p class="text-xs text-muted-foreground" x-text="activeTab === 'students' ? 'Murid' : 'Guru'"></p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-1 text-xs font-medium text-emerald-500 ring-1 ring-inset ring-emerald-500/20" x-text="item.time"></span>
                </div>
            </div>
        </template>

        <div x-show="(activeTab === 'students' && students.length === 0) || (activeTab === 'teachers' && teachers.length === 0)" class="text-center py-10 opacity-50">
            <i data-lucide="user-x" class="w-10 h-10 mx-auto mb-3"></i>
            <p class="text-sm">Belum ada presensi</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function liveAttendanceList() {
    return {
        activeTab: 'students',
        students: [],
        teachers: [],
        loading: true,
        
        init() {
            this.fetchData();
            // Automatically refresh every 10 seconds to catch new check-ins
            setInterval(() => this.fetchData(false), 10000);
            
            // Allow other components to trigger a refresh
            window.addEventListener('attendance-logged', () => {
                this.fetchData(true);
            });
        },
        
        fetchData(showLoading = true) {
            if (showLoading) this.loading = true;
            
            fetch('{{ route("attendance.live-list") }}')
                .then(res => res.json())
                .then(data => {
                    // Update arrays only if there are changes to prevent re-rendering flickers
                    if (JSON.stringify(this.students) !== JSON.stringify(data.students)) {
                        this.students = data.students;
                    }
                    if (JSON.stringify(this.teachers) !== JSON.stringify(data.teachers)) {
                        this.teachers = data.teachers;
                    }
                    this.$nextTick(() => window.lucide?.createIcons());
                })
                .catch(err => console.error('Error fetching live list:', err))
                .finally(() => this.loading = false);
        }
    }
}
</script>
@endpush
