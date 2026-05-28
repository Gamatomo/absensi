import { useState } from 'react';
import { motion } from 'motion/react';
import { CheckCircle, ScanFace, CreditCard, MapPin, Send } from 'lucide-react';

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

interface TeacherAttendanceFormProps {
  teacher: Teacher;
  onSubmit: (record: Omit<AttendanceRecord, 'id'>) => void;
}

export default function TeacherAttendanceForm({ teacher, onSubmit }: TeacherAttendanceFormProps) {
  const [step, setStep] = useState<1 | 2 | 3>(1); // 1: card, 2: face, 3: success
  const [location, setLocation] = useState('Gedung A - Lantai 1');
  const [cardScanned, setCardScanned] = useState(false);
  const [faceScanned, setFaceScanned] = useState(false);

  const handleCardScan = () => {
    setCardScanned(true);
    setTimeout(() => {
      setStep(2);
    }, 1000);
  };

  const handleFaceScan = () => {
    setFaceScanned(true);
    setTimeout(() => {
      const now = new Date();
      const currentTime = now.getHours() * 60 + now.getMinutes();
      const cutoffTime = 7 * 60; // 07:00

      let status: 'present' | 'late' | 'absent' = 'present';
      if (currentTime > cutoffTime) {
        status = 'late';
      }

      onSubmit({
        studentId: teacher.id,
        timestamp: now.toISOString(),
        method: 'card', // Both methods used, but stored as card for compatibility
        status,
        location
      });

      setStep(3);
      setTimeout(() => {
        setStep(1);
        setCardScanned(false);
        setFaceScanned(false);
      }, 3000);
    }, 1500);
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h2 className="flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
          <CheckCircle className="w-6 h-6 text-primary" />
          Absensi Guru
        </h2>
        <p className="text-sm text-muted-foreground mt-1">
          Lakukan absensi kehadiran Anda
        </p>
      </div>

      {/* Attendance Form */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          {/* Location Selection */}
          <div className="space-y-2 mb-6">
            <label htmlFor="location" className="block text-sm text-foreground">
              Lokasi
            </label>
            <div className="relative">
              <div className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                <MapPin className="w-5 h-5" />
              </div>
              <select
                id="location"
                value={location}
                onChange={(e) => setLocation(e.target.value)}
                className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              >
                <option value="Gedung A - Lantai 1">Gedung A - Lantai 1</option>
                <option value="Gedung A - Lantai 2">Gedung A - Lantai 2</option>
                <option value="Gedung B - Lantai 1">Gedung B - Lantai 1</option>
                <option value="Gedung B - Lantai 2">Gedung B - Lantai 2</option>
                <option value="Ruang Guru">Ruang Guru</option>
              </select>
            </div>
          </div>

          {/* Progress Indicator */}
          <div className="flex items-center justify-center gap-4 mb-8">
            <div className={`flex items-center gap-2 px-4 py-2 rounded-lg border ${
              step >= 1 ? 'bg-accent/10 border-accent/30 text-accent' : 'bg-secondary/30 border-border text-muted-foreground'
            }`}>
              <CreditCard className="w-4 h-4" />
              <span className="text-sm">1. Kartu RFID</span>
              {cardScanned && <CheckCircle className="w-4 h-4" />}
            </div>
            <div className="w-8 h-0.5 bg-border" />
            <div className={`flex items-center gap-2 px-4 py-2 rounded-lg border ${
              step >= 2 ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-secondary/30 border-border text-muted-foreground'
            }`}>
              <ScanFace className="w-4 h-4" />
              <span className="text-sm">2. Verifikasi Wajah</span>
              {faceScanned && <CheckCircle className="w-4 h-4" />}
            </div>
          </div>

          {/* Step 1: Card Scan */}
          {step === 1 && (
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="space-y-6"
            >
              <div className="text-center py-12 bg-accent/5 rounded-lg border-2 border-dashed border-accent/30">
                <motion.div
                  animate={{ scale: [1, 1.1, 1] }}
                  transition={{ duration: 2, repeat: Infinity }}
                  className="flex justify-center mb-4"
                >
                  <div className="p-6 bg-accent/10 rounded-full">
                    <CreditCard className="w-16 h-16 text-accent" />
                  </div>
                </motion.div>
                <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
                  Tempelkan Kartu RFID
                </h3>
                <p className="text-sm text-muted-foreground mb-6">
                  Dekatkan kartu RFID Anda ke pembaca
                </p>
                <button
                  onClick={handleCardScan}
                  className="px-6 py-3 bg-accent hover:bg-accent/90 text-white rounded-lg transition-all shadow-sm"
                >
                  Simulasi Scan Kartu
                </button>
              </div>
            </motion.div>
          )}

          {/* Step 2: Face Scan */}
          {step === 2 && (
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="space-y-6"
            >
              <div className="text-center py-12 bg-primary/5 rounded-lg border-2 border-dashed border-primary/30">
                <motion.div
                  animate={{ scale: [1, 1.1, 1] }}
                  transition={{ duration: 2, repeat: Infinity }}
                  className="flex justify-center mb-4"
                >
                  <div className="p-6 bg-primary/10 rounded-full">
                    <ScanFace className="w-16 h-16 text-primary" />
                  </div>
                </motion.div>
                <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
                  Verifikasi Wajah
                </h3>
                <p className="text-sm text-muted-foreground mb-6">
                  Posisikan wajah Anda di depan kamera untuk verifikasi
                </p>
                <button
                  onClick={handleFaceScan}
                  className="px-6 py-3 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
                >
                  Simulasi Scan Wajah
                </button>
              </div>
            </motion.div>
          )}

          {/* Step 3: Success */}
          {step === 3 && (
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="space-y-6"
            >
              <div className="text-center py-12 bg-chart-3/5 rounded-lg border-2 border-chart-3/30">
                <motion.div
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  transition={{ type: 'spring', stiffness: 200, damping: 10 }}
                  className="flex justify-center mb-4"
                >
                  <div className="p-6 bg-chart-3/10 rounded-full">
                    <CheckCircle className="w-16 h-16 text-chart-3" />
                  </div>
                </motion.div>
                <h3 className="text-chart-3 mb-2" style={{ fontFamily: 'var(--font-display)' }}>
                  Absensi Berhasil!
                </h3>
                <p className="text-sm text-muted-foreground">
                  Kartu dan wajah Anda telah terverifikasi
                </p>
              </div>
            </motion.div>
          )}

          {/* Info Notice */}
          <div className="mt-6 p-4 bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <p className="text-sm text-blue-900 dark:text-blue-200">
              <strong>Sistem Keamanan Ganda:</strong> Absensi menggunakan kartu RFID untuk identifikasi awal, kemudian diverifikasi dengan pengenalan wajah untuk memastikan kesesuaian data. Waktu terlambat: setelah 07:00 WIB.
            </p>
          </div>
        </div>
      </motion.div>

      {/* Info Card */}
      <div className="bg-card rounded-xl border border-border shadow-sm p-6">
        <h3 className="mb-3" style={{ fontFamily: 'var(--font-display)' }}>
          Informasi Anda
        </h3>
        <div className="space-y-2 text-sm">
          <div className="flex justify-between">
            <span className="text-muted-foreground">Nama:</span>
            <span className="text-foreground">{teacher.name}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">Mata Pelajaran:</span>
            <span className="text-foreground">{teacher.subject}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">ID Wajah:</span>
            <span className="text-foreground">{teacher.faceId || 'Belum terdaftar'}</span>
          </div>
          <div className="flex justify-between">
            <span className="text-muted-foreground">ID Kartu:</span>
            <span className="text-foreground">{teacher.cardId || 'Belum terdaftar'}</span>
          </div>
        </div>
      </div>
    </div>
  );
}
