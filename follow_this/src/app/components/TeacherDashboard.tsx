import { motion } from 'motion/react';
import {
  CheckCircle,
  Clock,
  XCircle,
  BarChart3,
  AlertCircle
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

interface TeacherDashboardProps {
  teacher: Teacher;
  attendanceRecords: AttendanceRecord[];
}

export default function TeacherDashboard({ teacher, attendanceRecords }: TeacherDashboardProps) {
  const teacherRecords = attendanceRecords.filter(r => r.studentId === teacher.id);

  const stats = {
    present: teacherRecords.filter(r => r.status === 'present').length,
    late: teacherRecords.filter(r => r.status === 'late').length,
    absent: teacherRecords.filter(r => r.status === 'absent').length
  };

  const totalAttendance = stats.present + stats.late + stats.absent;
  const attendanceRate = totalAttendance > 0
    ? ((stats.present + stats.late) / totalAttendance * 100).toFixed(1)
    : '0';

  return (
    <div className="space-y-6">
      {/* Welcome Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 text-primary-foreground shadow-lg">
          <h2 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
            Selamat Datang, {teacher.name}
          </h2>
          <p className="text-primary-foreground/90">
            Mata Pelajaran: {teacher.subject}
          </p>
        </div>
      </motion.div>

      {/* Statistics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.1 }}
        >
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
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.2 }}
        >
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
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.3 }}
        >
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
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.4 }}
        >
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
        </motion.div>
      </div>

      {/* Attendance Rules */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.5 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <AlertCircle className="w-5 h-5 text-primary" />
            Peraturan Tata Cara Absen
          </h3>
          <div className="space-y-3 text-sm text-muted-foreground">
            <div className="flex items-start gap-3">
              <div className="mt-0.5 w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
              <p>Absensi dilakukan dengan <strong className="text-foreground">dua tahap keamanan</strong>: scan kartu RFID terlebih dahulu, kemudian verifikasi wajah untuk memastikan kesesuaian data.</p>
            </div>
            <div className="flex items-start gap-3">
              <div className="mt-0.5 w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
              <p>Waktu absensi untuk guru dimulai pada <strong className="text-foreground">pukul 07:00 WIB</strong>.</p>
            </div>
            <div className="flex items-start gap-3">
              <div className="mt-0.5 w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
              <p>Guru yang melakukan absensi <strong className="text-foreground">setelah pukul 07:00 WIB</strong> akan tercatat sebagai terlambat.</p>
            </div>
            <div className="flex items-start gap-3">
              <div className="mt-0.5 w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
              <p>Pastikan kartu RFID dan data wajah Anda sudah terdaftar di sistem.</p>
            </div>
            <div className="flex items-start gap-3">
              <div className="mt-0.5 w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
              <p>Jika berhalangan hadir, segera ajukan cuti melalui menu <strong className="text-foreground">Pengajuan Cuti</strong>.</p>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
}
