import { useState } from 'react';
import { CalendarCheck, Search, ScanFace, CreditCard, MapPin, Clock, Filter } from 'lucide-react';

interface Student {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  department: string;
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

interface AttendanceHistoryProps {
  attendanceRecords: AttendanceRecord[];
  students: Student[];
}

export default function AttendanceHistory({ attendanceRecords, students }: AttendanceHistoryProps) {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterMethod, setFilterMethod] = useState<'all' | 'face' | 'card'>('all');
  const [filterStatus, setFilterStatus] = useState<'all' | 'present' | 'late' | 'absent'>('all');

  const getStudentName = (studentId: string) => {
    const student = students.find(s => s.id === studentId);
    return student?.name || studentId;
  };

  const getStudent = (studentId: string) => {
    return students.find(s => s.id === studentId);
  };

  const filteredRecords = attendanceRecords
    .filter(record => {
      const student = getStudent(record.studentId);
      const matchesSearch = student?.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                           record.studentId.toLowerCase().includes(searchTerm.toLowerCase()) ||
                           record.location?.toLowerCase().includes(searchTerm.toLowerCase());
      const matchesMethod = filterMethod === 'all' || record.method === filterMethod;
      const matchesStatus = filterStatus === 'all' || record.status === filterStatus;
      return matchesSearch && matchesMethod && matchesStatus;
    })
    .sort((a, b) => new Date(b.timestamp).getTime() - new Date(a.timestamp).getTime());

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-primary/10 rounded-lg">
              <CalendarCheck className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h2 style={{ fontFamily: 'var(--font-display)' }}>Riwayat Presensi</h2>
              <p className="text-sm text-muted-foreground">{attendanceRecords.length} total rekaman</p>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row gap-4">
          {/* Search */}
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input
              type="text"
              placeholder="Cari berdasarkan nama siswa, ID, atau lokasi..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            />
          </div>

          {/* Method Filter */}
          <div className="flex gap-2">
            <button
              onClick={() => setFilterMethod('all')}
              className={`flex items-center gap-2 px-4 py-3 rounded-lg transition-all ${
                filterMethod === 'all'
                  ? 'bg-primary text-primary-foreground shadow-sm'
                  : 'bg-secondary border border-border hover:border-primary/50'
              }`}
            >
              <Filter className="w-4 h-4" />
              Semua
            </button>
            <button
              onClick={() => setFilterMethod('face')}
              className={`flex items-center gap-2 px-4 py-3 rounded-lg transition-all ${
                filterMethod === 'face'
                  ? 'bg-primary text-primary-foreground shadow-sm'
                  : 'bg-secondary border border-border hover:border-primary/50'
              }`}
            >
              <ScanFace className="w-4 h-4" />
              Wajah
            </button>
            <button
              onClick={() => setFilterMethod('card')}
              className={`flex items-center gap-2 px-4 py-3 rounded-lg transition-all ${
                filterMethod === 'card'
                  ? 'bg-primary text-primary-foreground shadow-sm'
                  : 'bg-secondary border border-border hover:border-primary/50'
              }`}
            >
              <CreditCard className="w-4 h-4" />
              Kartu
            </button>
          </div>

          {/* Status Filter */}
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value as 'all' | 'present' | 'late' | 'absent')}
            className="px-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all cursor-pointer"
          >
            <option value="all">Semua Status</option>
            <option value="present">Hadir</option>
            <option value="late">Terlambat</option>
            <option value="absent">Tidak Hadir</option>
          </select>
        </div>
      </div>

      {/* Records List */}
      {filteredRecords.length > 0 ? (
        <div className="space-y-3">
          {filteredRecords.map((record) => {
            const student = getStudent(record.studentId);
            return (
              <div
                key={record.id}
                className="bg-card border border-border hover:border-primary/50 rounded-lg p-5 shadow-sm hover:shadow-md transition-all"
              >
                <div className="flex items-center gap-4">
                  {/* Method Icon */}
                  <div className={`p-2.5 rounded-lg ${
                    record.method === 'face' ? 'bg-primary/10' : 'bg-accent/10'
                  }`}>
                    {record.method === 'face' ? (
                      <ScanFace className="w-5 h-5 text-primary" />
                    ) : (
                      <CreditCard className="w-5 h-5 text-accent" />
                    )}
                  </div>

                  {/* Student Info */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-4 mb-2">
                      <div className="min-w-0">
                        <h3 className="truncate" style={{ fontFamily: 'var(--font-display)' }}>
                          {student?.name || record.studentId}
                        </h3>
                        <p className="text-sm text-muted-foreground font-mono truncate">
                          {record.studentId}
                          {student?.department && ` • ${student.department}`}
                        </p>
                      </div>

                      {/* Status Badge */}
                      <span className={`shrink-0 px-3 py-1 rounded-md text-xs border ${
                        record.status === 'present' ? 'bg-green-500/10 text-green-600 border-green-500/20' :
                        record.status === 'late' ? 'bg-orange-500/10 text-orange-600 border-orange-500/20' :
                        'bg-red-500/10 text-red-600 border-red-500/20'
                      }`}>
                        {record.status === 'present' ? 'Hadir' : record.status === 'late' ? 'Terlambat' : 'Tidak Hadir'}
                      </span>
                    </div>

                    <div className="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                      <div className="flex items-center gap-2">
                        <Clock className="w-4 h-4" />
                        <span>{new Date(record.timestamp).toLocaleString('id-ID')}</span>
                      </div>

                      {record.location && (
                        <div className="flex items-center gap-2">
                          <MapPin className="w-4 h-4" />
                          <span>{record.location}</span>
                        </div>
                      )}

                      <div className={`flex items-center gap-2 px-2 py-1 rounded-md border ${
                        record.method === 'face' ? 'bg-primary/10 border-primary/20' : 'bg-accent/10 border-accent/20'
                      }`}>
                        {record.method === 'face' ? (
                          <>
                            <ScanFace className="w-3 h-3 text-primary" />
                            <span className="text-xs text-primary">Pengenalan Wajah</span>
                          </>
                        ) : (
                          <>
                            <CreditCard className="w-3 h-3 text-accent" />
                            <span className="text-xs text-accent">Akses Kartu</span>
                          </>
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      ) : (
        <div className="bg-card border border-border rounded-lg p-12 shadow-sm text-center">
          <div className="inline-flex p-6 bg-secondary rounded-lg mb-4">
            <CalendarCheck className="w-12 h-12 text-muted-foreground" />
          </div>
          <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
            {attendanceRecords.length === 0 ? 'Belum Ada Data Presensi' : 'Tidak Ada Hasil'}
          </h3>
          <p className="text-muted-foreground">
            {attendanceRecords.length === 0
              ? 'Unggah data presensi untuk melihat riwayat'
              : 'Coba sesuaikan pencarian atau filter Anda'}
          </p>
        </div>
      )}
    </div>
  );
}
