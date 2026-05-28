import { motion } from 'motion/react';
import {
  User,
  Calendar,
  CheckCircle,
  XCircle,
  Clock,
  TrendingUp,
  Mail,
  IdCard,
  Building2,
  ScanFace,
  CreditCard,
  ClipboardList
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

interface StudentDashboardProps {
  student: Student;
  attendanceRecords: AttendanceRecord[];
}

export default function StudentDashboard({ student, attendanceRecords }: StudentDashboardProps) {
  // Filter attendance records for this student
  const myAttendance = attendanceRecords.filter(record => record.studentId === student.id);

  // Calculate statistics
  const totalDays = myAttendance.length;
  const presentCount = myAttendance.filter(r => r.status === 'present').length;
  const lateCount = myAttendance.filter(r => r.status === 'late').length;
  const absentCount = myAttendance.filter(r => r.status === 'absent').length;
  const attendanceRate = totalDays > 0 ? ((presentCount + lateCount) / totalDays * 100).toFixed(1) : '0';

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <div className="space-y-6">
      {/* Welcome Section */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-gradient-to-r from-primary to-primary/80 rounded-xl p-8 text-primary-foreground shadow-lg border border-primary/20">
          <div className="flex items-center gap-4">
            <div className="bg-primary-foreground/20 p-4 rounded-full backdrop-blur-sm border border-primary-foreground/30">
              <User className="w-12 h-12 text-primary-foreground" />
            </div>
            <div>
              <h2 className="text-primary-foreground mb-1" style={{ fontFamily: 'var(--font-display)' }}>
                Selamat Datang, {student.name}
              </h2>
              <p className="text-primary-foreground/80 text-sm">
                Dashboard Presensi Pribadi
              </p>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Profile Card */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <IdCard className="w-5 h-5 text-primary" />
            Informasi Profil
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border">
              <Mail className="w-5 h-5 text-muted-foreground" />
              <div>
                <p className="text-xs text-muted-foreground">Email</p>
                <p className="text-foreground">{student.email}</p>
              </div>
            </div>
            <div className="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border">
              <Building2 className="w-5 h-5 text-muted-foreground" />
              <div>
                <p className="text-xs text-muted-foreground">Jurusan</p>
                <p className="text-foreground">{student.department}</p>
              </div>
            </div>
            <div className="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border">
              <Calendar className="w-5 h-5 text-muted-foreground" />
              <div>
                <p className="text-xs text-muted-foreground">Tanggal Daftar</p>
                <p className="text-foreground">{formatDate(student.enrolledDate)}</p>
              </div>
            </div>
            <div className="flex items-center gap-3 p-3 bg-secondary/50 rounded-lg border border-border">
              <IdCard className="w-5 h-5 text-muted-foreground" />
              <div>
                <p className="text-xs text-muted-foreground">NIS/NISN</p>
                <p className="text-foreground">{student.nisn || student.id}</p>
              </div>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Statistics Cards */}
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

      {/* Peraturan Tata Cara Absen */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.4 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <ClipboardList className="w-5 h-5 text-primary" />
            Peraturan Tata Cara Absen
          </h3>
          <div className="space-y-4">
            <div className="p-4 bg-primary/5 rounded-lg border border-primary/20">
              <h4 className="flex items-center gap-2 mb-3 text-primary">
                <CheckCircle className="w-4 h-4" />
                Waktu Kehadiran
              </h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Siswa wajib hadir sebelum jam <strong>07:30 WIB</strong></span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Keterlambatan di atas jam 07:30 WIB akan dicatat sebagai <strong>terlambat</strong></span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Keterlambatan lebih dari 15 menit tanpa keterangan akan dicatat sebagai <strong>tidak hadir</strong></span>
                </li>
              </ul>
            </div>

            <div className="p-4 bg-secondary/50 rounded-lg border border-border">
              <h4 className="flex items-center gap-2 mb-3 text-foreground">
                <CheckCircle className="w-4 h-4 text-primary" />
                Metode Absensi
              </h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li className="flex items-start gap-2">
                  <ScanFace className="w-4 h-4 text-primary mt-0.5" />
                  <span><strong>Pengenalan Wajah:</strong> Posisikan wajah di depan kamera dengan pencahayaan yang cukup</span>
                </li>
                <li className="flex items-start gap-2">
                  <CreditCard className="w-4 h-4 text-accent mt-0.5" />
                  <span><strong>Kartu Identitas:</strong> Tempelkan kartu pada reader yang tersedia di pintu masuk</span>
                </li>
              </ul>
            </div>

            <div className="p-4 bg-secondary/50 rounded-lg border border-border">
              <h4 className="flex items-center gap-2 mb-3 text-foreground">
                <CheckCircle className="w-4 h-4 text-primary" />
                Ketentuan Izin
              </h4>
              <ul className="space-y-2 text-sm text-muted-foreground">
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Izin sakit harus disertai surat keterangan dokter</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Izin keperluan keluarga harus diajukan <strong>H-1</strong> melalui sistem</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-primary mt-1">•</span>
                  <span>Absensi tidak dapat diubah setelah <strong>24 jam</strong></span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
}
