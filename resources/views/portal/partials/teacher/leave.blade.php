@php
    $teacher = $currentTeacher;
    $myRequests = collect($leaveRequests)->where('userId', (string) ($teacher['userId'] ?? ''));
@endphp
<div class="space-y-6" x-data="{
    loading: false,
    message: '',
    messageType: '',
    submitLeaveRequest(event) {
        this.loading = true;
        this.message = '';
        const formData = new FormData(event.target);
        fetch('{{ route('leave-requests.store') }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            this.message = data.message;
            this.messageType = data.success ? 'success' : 'error';
            if (data.success) {
                event.target.reset();
                setTimeout(() => window.location.reload(), 1500);
            }
        })
        .catch(e => {
            this.message = 'Terjadi kesalahan: ' + e.message;
            this.messageType = 'error';
        })
        .finally(() => this.loading = false);
    }
}">
    <div class="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h2 class="font-display mb-4 flex items-center gap-2"><x-icon name="file-text" class="w-5 h-5 text-primary"/>Pengajuan Cuti</h2>
        <template x-if="message">
            <div :class="messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" class="mb-4 p-3 border rounded-lg text-sm" x-text="message"></div>
        </template>
        <form @submit.prevent="submitLeaveRequest($event)" class="grid md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-sm text-muted-foreground">Alasan</label>
                <input name="reason" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background" placeholder="Sakit / Cuti tahunan">
            </div>
            <div>
                <label class="text-sm text-muted-foreground">Tanggal Mulai</label>
                <input type="date" name="start_date" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
            </div>
            <div>
                <label class="text-sm text-muted-foreground">Tanggal Selesai</label>
                <input type="date" name="end_date" required class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background">
            </div>
            <div class="md:col-span-2">
                <label class="text-sm text-muted-foreground">Keterangan</label>
                <textarea name="description" rows="3" class="w-full mt-1 px-3 py-2 border border-border rounded-lg bg-background"></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" :disabled="loading" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg disabled:opacity-50">
                    <span x-show="!loading">Kirim Pengajuan Cuti</span>
                    <span x-show="loading">Mengirim...</span>
                </button>
            </div>
        </form>
    </div>
    <div class="space-y-3">
        @forelse($myRequests as $request)
        <div class="bg-card border border-border rounded-lg p-4">
            <div class="flex justify-between"><p class="font-medium">{{ $request['reason'] }}</p>
            <span class="text-xs uppercase px-2 py-1 rounded border @if(($request['status'] ?? '') === 'approved') bg-green-50 border-green-200 text-green-700 @elseif(($request['status'] ?? '') === 'rejected') bg-red-50 border-red-200 text-red-700 @else bg-yellow-50 border-yellow-200 text-yellow-700 @endif">{{ $request['status'] }}</span></div>
            <p class="text-sm text-muted-foreground mt-1">{{ $request['startDate'] }} – {{ $request['endDate'] }}</p>
        </div>
        @empty
        <p class="text-sm text-muted-foreground">Belum ada pengajuan cuti.</p>
        @endforelse
    </div>
</div>

