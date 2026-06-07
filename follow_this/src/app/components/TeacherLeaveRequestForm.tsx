import { useState } from 'react';
import { motion } from 'motion/react';
import { FileText, Calendar, Send, X, Clock } from 'lucide-react';

interface Teacher {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  subject: string;
  enrolledDate: string;
}

interface LeaveRequest {
  id: string;
  studentId: string;
  reason: string;
  startDate: string;
  endDate: string;
  description: string;
  status: 'pending' | 'approved' | 'rejected';
  submittedAt: string;
}

interface TeacherLeaveRequestFormProps {
  teacher: Teacher;
  onSubmit: (request: Omit<LeaveRequest, 'id' | 'status' | 'submittedAt'>) => void;
  requests: LeaveRequest[];
}

export default function TeacherLeaveRequestForm({ teacher, onSubmit, requests }: TeacherLeaveRequestFormProps) {
  const [reason, setReason] = useState('');
  const [startDate, setStartDate] = useState('');
  const [description, setDescription] = useState('');
  const [showForm, setShowForm] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (!reason || !startDate || !description) {
      alert('Semua field harus diisi');
      return;
    }

    onSubmit({
      studentId: teacher.id,
      reason,
      startDate,
      endDate: startDate, // Same as startDate for single-day leave
      description
    });

    // Reset form
    setReason('');
    setStartDate('');
    setDescription('');
    setShowForm(false);
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved':
        return 'text-chart-3 bg-chart-3/10 border-chart-3/30';
      case 'rejected':
        return 'text-chart-5 bg-chart-5/10 border-chart-5/30';
      case 'pending':
        return 'text-chart-4 bg-chart-4/10 border-chart-4/30';
      default:
        return 'text-muted-foreground bg-secondary border-border';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'approved':
        return 'Disetujui';
      case 'rejected':
        return 'Ditolak';
      case 'pending':
        return 'Menunggu';
      default:
        return status;
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <FileText className="w-6 h-6 text-primary" />
            Pengajuan Cuti
          </h2>
          <p className="text-sm text-muted-foreground mt-1">
            Ajukan permohonan cuti tidak hadir
          </p>
        </div>
        <button
          onClick={() => setShowForm(!showForm)}
          className="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
        >
          {showForm ? (
            <>
              <X className="w-4 h-4" />
              Batal
            </>
          ) : (
            <>
              <FileText className="w-4 h-4" />
              Ajukan Cuti Baru
            </>
          )}
        </button>
      </div>

      {/* Form */}
      {showForm && (
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.3 }}
        >
          <div className="bg-card rounded-xl border border-border shadow-sm p-6">
            <h3 className="mb-4" style={{ fontFamily: 'var(--font-display)' }}>
              Form Pengajuan Cuti
            </h3>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-2">
                <label htmlFor="reason" className="block text-sm text-foreground">
                  Alasan Cuti
                </label>
                <select
                  id="reason"
                  value={reason}
                  onChange={(e) => setReason(e.target.value)}
                  className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                  required
                >
                  <option value="">Pilih alasan cuti</option>
                  <option value="Sakit">Sakit</option>
                  <option value="Keperluan Keluarga">Keperluan Keluarga</option>
                  <option value="Keperluan Dinas">Keperluan Dinas</option>
                  <option value="Cuti Tahunan">Cuti Tahunan</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>

              <div className="space-y-2">
                <label htmlFor="startDate" className="block text-sm text-foreground">
                  Tanggal Cuti
                </label>
                <input
                  id="startDate"
                  type="date"
                  value={startDate}
                  onChange={(e) => setStartDate(e.target.value)}
                  className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                  required
                />
              </div>

              <div className="space-y-2">
                <label htmlFor="description" className="block text-sm text-foreground">
                  Keterangan
                </label>
                <textarea
                  id="description"
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  rows={4}
                  className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all resize-none"
                  placeholder="Jelaskan detail alasan cuti Anda..."
                  required
                />
              </div>

              <button
                type="submit"
                className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
              >
                <Send className="w-4 h-4" />
                Kirim Pengajuan
              </button>
            </form>
          </div>
        </motion.div>
      )}

      {/* Requests History */}
      <div className="bg-card rounded-xl border border-border shadow-sm p-6">
        <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
          <Clock className="w-5 h-5 text-primary" />
          Riwayat Pengajuan
        </h3>

        {requests.length === 0 ? (
          <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
            <FileText className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
            <p className="text-muted-foreground">Belum ada pengajuan cuti</p>
          </div>
        ) : (
          <div className="space-y-3">
            {requests.map((request, index) => (
              <motion.div
                key={request.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.3, delay: index * 0.05 }}
                className="p-4 bg-secondary/30 rounded-lg border border-border hover:bg-secondary/50 transition-colors"
              >
                <div className="flex items-start justify-between mb-3">
                  <div>
                    <h4 className="text-foreground">{request.reason}</h4>
                    <p className="text-sm text-muted-foreground mt-1">
                      {formatDate(request.startDate)} - {formatDate(request.endDate)}
                    </p>
                  </div>
                  <span className={`px-3 py-1 rounded-full text-sm border ${getStatusColor(request.status)}`}>
                    {getStatusText(request.status)}
                  </span>
                </div>
                <p className="text-sm text-muted-foreground mb-2">
                  {request.description}
                </p>
                <p className="text-xs text-muted-foreground">
                  Diajukan: {formatDate(request.submittedAt)}
                </p>
              </motion.div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
