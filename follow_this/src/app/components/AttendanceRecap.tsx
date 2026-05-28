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
  TrendingUp,
  FileText
} from 'lucide-react';

interface Student {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  department: string;
  enrolledDate: string;
  nisn?: string;
}

interface AttendanceRecord {
  id: string;
  studentId: string;
  timestamp: string;
  method: 'face' | 'card';
  status: 'present' | 'late' | 'absent';
  location?: string;
}

interface AttendanceRecapProps {
  student: Student;
  attendanceRecords: AttendanceRecord[];
}

export default function AttendanceRecap({ student, attendanceRecords }: AttendanceRecapProps) {
  const [filterMonth, setFilterMonth] = useState(new Date().toISOString().slice(0, 7));
  const [filterStatus, setFilterStatus] = useState<string>('all');

  // Filter attendance records
  const myAttendance = attendanceRecords.filter(record => record.studentId === student.id);

  const filteredAttendance = myAttendance.filter(record => {
    const recordDate = new Date(record.timestamp);
    const recordMonth = recordDate.toISOString().slice(0, 7);

    const matchesMonth = recordMonth === filterMonth;
    const matchesStatus = filterStatus === 'all' || record.status === filterStatus;

    return matchesMonth && matchesStatus;
  });

  // Calculate statistics for filtered period
  const totalDays = filteredAttendance.length;
  const presentCount = filteredAttendance.filter(r => r.status === 'present').length;
  const lateCount = filteredAttendance.filter(r => r.status === 'late').length;
  const absentCount = filteredAttendance.filter(r => r.status === 'absent').length;
  const attendanceRate = totalDays > 0 ? ((presentCount + lateCount) / totalDays * 100).toFixed(1) : '0';

  // Overall statistics
  const overallTotal = myAttendance.length;
  const overallPresent = myAttendance.filter(r => r.status === 'present').length;
  const overallLate = myAttendance.filter(r => r.status === 'late').length;
  const overallAbsent = myAttendance.filter(r => r.status === 'absent').length;
  const overallRate = overallTotal > 0 ? ((overallPresent + overallLate) / overallTotal * 100).toFixed(1) : '0';

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const formatTime = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
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

  const handleExport = () => {
    // Simple CSV export
    let csv = 'Tanggal,Waktu,Status,Metode,Lokasi\n';

    filteredAttendance.forEach(record => {
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
    a.download = `rekap-presensi-${student.name}-${filterMonth}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <FileText className="w-6 h-6 text-primary" />
            Rekap Absensi
          </h2>
          <p className="text-sm text-muted-foreground mt-1">
            Lihat dan unduh rekap kehadiran Anda
          </p>
        </div>
        <button
          onClick={handleExport}
          disabled={filteredAttendance.length === 0}
          className="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <Download className="w-4 h-4" />
          Export CSV
        </button>
      </div>

      {/* Overall Statistics */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-6 text-primary-foreground shadow-lg border border-primary/20">
          <h3 className="text-primary-foreground mb-4" style={{ fontFamily: 'var(--font-display)' }}>
            Statistik Keseluruhan
          </h3>
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div className="bg-primary-foreground/10 rounded-lg p-4 backdrop-blur-sm border border-primary-foreground/20">
              <p className="text-primary-foreground/80 text-xs mb-1">Total Hari</p>
              <p className="text-primary-foreground text-2xl">{overallTotal}</p>
            </div>
            <div className="bg-primary-foreground/10 rounded-lg p-4 backdrop-blur-sm border border-primary-foreground/20">
              <p className="text-primary-foreground/80 text-xs mb-1">Tingkat Kehadiran</p>
              <p className="text-primary-foreground text-2xl">{overallRate}%</p>
            </div>
            <div className="bg-primary-foreground/10 rounded-lg p-4 backdrop-blur-sm border border-primary-foreground/20">
              <p className="text-primary-foreground/80 text-xs mb-1">Hadir</p>
              <p className="text-primary-foreground text-2xl">{overallPresent}</p>
            </div>
            <div className="bg-primary-foreground/10 rounded-lg p-4 backdrop-blur-sm border border-primary-foreground/20">
              <p className="text-primary-foreground/80 text-xs mb-1">Terlambat</p>
              <p className="text-primary-foreground text-2xl">{overallLate}</p>
            </div>
            <div className="bg-primary-foreground/10 rounded-lg p-4 backdrop-blur-sm border border-primary-foreground/20">
              <p className="text-primary-foreground/80 text-xs mb-1">Tidak Hadir</p>
              <p className="text-primary-foreground text-2xl">{overallAbsent}</p>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Filters */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
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

      {/* Period Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.2 }}
        >
          <div className="bg-card rounded-xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="bg-primary/10 p-3 rounded-lg border border-primary/20">
                <TrendingUp className="w-6 h-6 text-primary" />
              </div>
            </div>
            <p className="text-sm text-muted-foreground mb-1">Tingkat Kehadiran</p>
            <h3 className="text-primary">{attendanceRate}%</h3>
            <p className="text-xs text-muted-foreground mt-1">Periode ini</p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.25 }}
        >
          <div className="bg-card rounded-xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="bg-chart-3/10 p-3 rounded-lg border border-chart-3/20">
                <CheckCircle className="w-6 h-6 text-chart-3" />
              </div>
            </div>
            <p className="text-sm text-muted-foreground mb-1">Hadir</p>
            <h3 className="text-chart-3">{presentCount}</h3>
            <p className="text-xs text-muted-foreground mt-1">dari {totalDays} hari</p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.3 }}
        >
          <div className="bg-card rounded-xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="bg-chart-4/10 p-3 rounded-lg border border-chart-4/20">
                <Clock className="w-6 h-6 text-chart-4" />
              </div>
            </div>
            <p className="text-sm text-muted-foreground mb-1">Terlambat</p>
            <h3 className="text-chart-4">{lateCount}</h3>
            <p className="text-xs text-muted-foreground mt-1">dari {totalDays} hari</p>
          </div>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.35 }}
        >
          <div className="bg-card rounded-xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="bg-chart-5/10 p-3 rounded-lg border border-chart-5/20">
                <XCircle className="w-6 h-6 text-chart-5" />
              </div>
            </div>
            <p className="text-sm text-muted-foreground mb-1">Tidak Hadir</p>
            <h3 className="text-chart-5">{absentCount}</h3>
            <p className="text-xs text-muted-foreground mt-1">dari {totalDays} hari</p>
          </div>
        </motion.div>
      </div>

      {/* Attendance Records Table */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.4 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <Calendar className="w-5 h-5 text-primary" />
            Detail Presensi
          </h3>

          {filteredAttendance.length === 0 ? (
            <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
              <Calendar className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
              <p className="text-muted-foreground">Tidak ada data presensi untuk periode ini</p>
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-border">
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Tanggal</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Waktu</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Status</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Metode</th>
                    <th className="text-left py-3 px-4 text-sm text-muted-foreground">Lokasi</th>
                  </tr>
                </thead>
                <tbody>
                  {filteredAttendance
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
                        <td className="py-3 px-4 text-foreground">
                          {formatTime(record.timestamp)}
                        </td>
                        <td className="py-3 px-4">
                          <span className={`px-3 py-1 rounded-full text-sm border inline-flex items-center gap-1.5 ${getStatusColor(record.status)}`}>
                            {record.status === 'present' && <CheckCircle className="w-3.5 h-3.5" />}
                            {record.status === 'late' && <Clock className="w-3.5 h-3.5" />}
                            {record.status === 'absent' && <XCircle className="w-3.5 h-3.5" />}
                            {getStatusText(record.status)}
                          </span>
                        </td>
                        <td className="py-3 px-4">
                          <div className="flex items-center gap-2">
                            <div className="flex items-center gap-1.5 px-2 py-1 bg-primary/10 rounded-md border border-primary/20">
                              <ScanFace className="w-4 h-4 text-primary" />
                              <span className="text-sm text-foreground">Wajah</span>
                            </div>
                            <div className="flex items-center gap-1.5 px-2 py-1 bg-accent/10 rounded-md border border-accent/20">
                              <CreditCard className="w-4 h-4 text-accent" />
                              <span className="text-sm text-foreground">Kartu</span>
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
