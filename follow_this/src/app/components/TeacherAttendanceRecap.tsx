import { useState } from 'react';
import { motion } from 'motion/react';
import {
  Calendar,
  Download,
  Filter,
  CheckCircle,
  XCircle,
  Clock,
  ScanFace,
  CreditCard,
  BarChart3
} from 'lucide-react';

interface Teacher {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  subject: string;
  enrolledDate: string;
}

interface AttendanceRecord {
  id: string;
  studentId: string;
  timestamp: string;
  method: 'face' | 'card';
  status: 'present' | 'late' | 'absent';
  location?: string;
}

interface TeacherAttendanceRecapProps {
  teacher: Teacher;
  attendanceRecords: AttendanceRecord[];
}

export default function TeacherAttendanceRecap({ teacher, attendanceRecords }: TeacherAttendanceRecapProps) {
  const [filterMonth, setFilterMonth] = useState(new Date().toISOString().slice(0, 7));
  const [filterStatus, setFilterStatus] = useState<string>('all');

  const teacherRecords = attendanceRecords.filter(r => r.studentId === teacher.id);

  const filteredRecords = teacherRecords.filter(record => {
    const recordMonth = new Date(record.timestamp).toISOString().slice(0, 7);
    const matchesMonth = recordMonth === filterMonth;
    const matchesStatus = filterStatus === 'all' || record.status === filterStatus;
    return matchesMonth && matchesStatus;
  });

  const stats = {
    total: filteredRecords.length,
    present: filteredRecords.filter(r => r.status === 'present').length,
    late: filteredRecords.filter(r => r.status === 'late').length,
    absent: filteredRecords.filter(r => r.status === 'absent').length
  };

  const attendanceRate = stats.total > 0
    ? ((stats.present + stats.late) / stats.total * 100).toFixed(1)
    : '0';

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'present':
        return 'text-chart-3 bg-chart-3/10 border-chart-3/30';
      case 'late':
        return 'text-chart-4 bg-chart-4/10 border-chart-4/30';
      case 'absent':
        return 'text-chart-5 bg-chart-5/10 border-chart-5/30';
      default:
        return 'text-muted-foreground bg-secondary border-border';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'present':
        return 'Hadir';
      case 'late':
        return 'Terlambat';
      case 'absent':
        return 'Tidak Hadir';
      default:
        return status;
    }
  };

  const exportData = () => {
    let csv = 'Tanggal,Waktu,Status,Metode,Lokasi\n';
    filteredRecords
      .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime())
      .forEach(record => {
        const date = formatDate(record.timestamp);
        const time = formatTime(record.timestamp);
        const status = getStatusText(record.status);
        const method = record.method === 'face' ? 'Wajah' : 'Kartu';
        const location = record.location || '-';
        csv += `"${date}","${time}","${status}","${method}","${location}"\n`;
      });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `rekap-absensi-${teacher.name}-${filterMonth}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 style={{ fontFamily: 'var(--font-display)' }}>
            Rekap Absensi
          </h2>
          <p className="text-sm text-muted-foreground mt-1">
            Lihat riwayat kehadiran Anda
          </p>
        </div>
        <button
          onClick={exportData}
          className="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
        >
          <Download className="w-4 h-4" />
          Export CSV
        </button>
      </div>

      {/* Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-card border border-border rounded-lg p-4 shadow-sm">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-primary/10 rounded-lg">
              <BarChart3 className="w-5 h-5 text-primary" />
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Tingkat Kehadiran</p>
              <h3 className="text-2xl text-primary" style={{ fontFamily: 'var(--font-display)' }}>
                {attendanceRate}%
              </h3>
            </div>
          </div>
        </div>

        <div className="bg-card border border-border rounded-lg p-4 shadow-sm">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-chart-3/10 rounded-lg">
              <CheckCircle className="w-5 h-5 text-chart-3" />
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Hadir</p>
              <h3 className="text-2xl text-chart-3" style={{ fontFamily: 'var(--font-display)' }}>
                {stats.present}
              </h3>
            </div>
          </div>
        </div>

        <div className="bg-card border border-border rounded-lg p-4 shadow-sm">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-chart-4/10 rounded-lg">
              <Clock className="w-5 h-5 text-chart-4" />
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Terlambat</p>
              <h3 className="text-2xl text-chart-4" style={{ fontFamily: 'var(--font-display)' }}>
                {stats.late}
              </h3>
            </div>
          </div>
        </div>

        <div className="bg-card border border-border rounded-lg p-4 shadow-sm">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-chart-5/10 rounded-lg">
              <XCircle className="w-5 h-5 text-chart-5" />
            </div>
            <div>
              <p className="text-sm text-muted-foreground">Tidak Hadir</p>
              <h3 className="text-2xl text-chart-5" style={{ fontFamily: 'var(--font-display)' }}>
                {stats.absent}
              </h3>
            </div>
          </div>
        </div>
      </div>

      {/* Filters */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <Filter className="w-5 h-5 text-primary" />
            Filter Data
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <label htmlFor="filterMonth" className="block text-sm text-foreground">
                Periode Bulan
              </label>
              <input
                id="filterMonth"
                type="month"
                value={filterMonth}
                onChange={(e) => setFilterMonth(e.target.value)}
                className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              />
            </div>
            <div className="space-y-2">
              <label htmlFor="filterStatus" className="block text-sm text-foreground">
                Status Kehadiran
              </label>
              <select
                id="filterStatus"
                value={filterStatus}
                onChange={(e) => setFilterStatus(e.target.value)}
                className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              >
                <option value="all">Semua Status</option>
                <option value="present">Hadir</option>
                <option value="late">Terlambat</option>
                <option value="absent">Tidak Hadir</option>
              </select>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Records Table */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <Calendar className="w-5 h-5 text-primary" />
            Riwayat Absensi
          </h3>

          {filteredRecords.length === 0 ? (
            <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
              <Calendar className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
              <p className="text-muted-foreground">Tidak ada data absensi</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-border">
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Tanggal</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Waktu</th>
                    <th className="text-center py-3 px-4 text-sm text-muted-foreground">Status</th>
                    <th className="text-center py-3 px-4 text-sm text-muted-foreground">Metode</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Lokasi</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredRecords
                    .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime())
                    .map((record, index) => (
                      <motion.tr
                        key={record.id}
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.3, delay: index * 0.03 }}
                        className="border-b border-border hover:bg-secondary/30 transition-colors"
                      >
                        <td className="py-3 px-4 text-foreground">
                          {formatDate(record.timestamp)}
                        </td>
                        <td className="py-3 px-4 text-muted-foreground text-sm">
                          {formatTime(record.timestamp)}
                        </td>
                        <td className="py-3 px-4 text-center">
                          <span className={`inline-block px-3 py-1 rounded-full text-sm border ${getStatusColor(record.status)}`}>
                            {getStatusText(record.status)}
                          </span>
                        </td>
                        <td className="py-3 px-4">
                          <div className="flex items-center justify-center gap-2">
                            <div className="flex items-center gap-1 px-2 py-1 bg-primary/10 rounded border border-primary/20">
                              <ScanFace className="w-3.5 h-3.5 text-primary" />
                              <span className="text-xs text-primary">Wajah</span>
                            </div>
                            <div className="flex items-center gap-1 px-2 py-1 bg-accent/10 rounded border border-accent/20">
                              <CreditCard className="w-3.5 h-3.5 text-accent" />
                              <span className="text-xs text-accent">Kartu</span>
                            </div>
                          </div>
                        </td>
                        <td className="py-3 px-4 text-muted-foreground text-sm">
                          {record.location || '-'}
                        </td>
                      </motion.tr>
                    ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </motion.div>
    </div>
  );
}
