import { useState } from 'react';
import { motion } from 'motion/react';
import {
  Calendar,
  Download,
  Filter,
  CheckCircle,
  XCircle,
  Clock,
  Users,
  GraduationCap,
  Search
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

interface AdminAttendanceRecapProps {
  students: Student[];
  teachers: Teacher[];
  attendanceRecords: AttendanceRecord[];
}

export default function AdminAttendanceRecap({
  students,
  teachers,
  attendanceRecords
}: AdminAttendanceRecapProps) {
  const [activeView, setActiveView] = useState<'students' | 'teachers'>('students');
  const [filterMonth, setFilterMonth] = useState(new Date().toISOString().slice(0, 7));
  const [searchQuery, setSearchQuery] = useState('');

  // Calculate attendance statistics per person
  const calculateStats = (personId: string) => {
    const records = attendanceRecords.filter(r => {
      const recordMonth = new Date(r.timestamp).toISOString().slice(0, 7);
      return r.studentId === personId && recordMonth === filterMonth;
    });

    const total = records.length;
    const present = records.filter(r => r.status === 'present').length;
    const late = records.filter(r => r.status === 'late').length;
    const absent = records.filter(r => r.status === 'absent').length;
    const rate = total > 0 ? ((present + late) / total * 100).toFixed(1) : '0';

    return { total, present, late, absent, rate };
  };

  const filteredStudents = students.filter(student =>
    student.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    student.department.toLowerCase().includes(searchQuery.toLowerCase()) ||
    (student.nisn && student.nisn.includes(searchQuery))
  );

  const filteredTeachers = teachers.filter(teacher =>
    teacher.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    teacher.subject.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const exportData = () => {
    const data = activeView === 'students' ? filteredStudents : filteredTeachers;
    let csv = 'Nama,Email,';
    csv += activeView === 'students' ? 'Jurusan,NIS/NISN,' : 'Mata Pelajaran,';
    csv += 'Total Hari,Hadir,Terlambat,Tidak Hadir,Tingkat Kehadiran\n';

    data.forEach(person => {
      const stats = calculateStats(person.id);
      const dept = activeView === 'students' ? (person as Student).department : (person as Teacher).subject;
      const extra = activeView === 'students' ? (person as Student).nisn || person.id : '';

      csv += `"${person.name}","${person.email}","${dept}",`;
      if (activeView === 'students') csv += `"${extra}",`;
      csv += `${stats.total},${stats.present},${stats.late},${stats.absent},${stats.rate}%\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `rekap-${activeView}-${filterMonth}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
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
            Kelola dan unduh rekap kehadiran siswa dan guru
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

      {/* View Toggle */}
      <div className="flex items-center gap-3">
        <div className="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
          <button
            onClick={() => setActiveView('students')}
            className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
              activeView === 'students'
                ? 'bg-primary text-primary-foreground shadow-sm'
                : 'hover:bg-secondary/80'
            }`}
          >
            <Users className="w-4 h-4" />
            Siswa
          </button>
          <button
            onClick={() => setActiveView('teachers')}
            className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
              activeView === 'teachers'
                ? 'bg-primary text-primary-foreground shadow-sm'
                : 'hover:bg-secondary/80'
            }`}
          >
            <GraduationCap className="w-4 h-4" />
            Guru
          </button>
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
              <label htmlFor="search" className="block text-sm text-foreground">
                Pencarian
              </label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
                <input
                  id="search"
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder={`Cari ${activeView === 'students' ? 'siswa' : 'guru'}...`}
                  className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                />
              </div>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Data Table */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <Calendar className="w-5 h-5 text-primary" />
            Rekap {activeView === 'students' ? 'Siswa' : 'Guru'} - {formatDate(filterMonth + '-01')}
          </h3>

          {activeView === 'students' ? (
            filteredStudents.length === 0 ? (
              <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
                <Users className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
                <p className="text-muted-foreground">Tidak ada data siswa</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b border-border">
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">NIS/NISN</th>
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">Nama</th>
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">Jurusan</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Total</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Hadir</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Terlambat</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Tidak Hadir</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Tingkat Kehadiran</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredStudents.map((student, index) => {
                      const stats = calculateStats(student.id);
                      return (
                        <motion.tr
                          key={student.id}
                          initial={{ opacity: 0, x: -20 }}
                          animate={{ opacity: 1, x: 0 }}
                          transition={{ duration: 0.3, delay: index * 0.03 }}
                          className="border-b border-border hover:bg-secondary/30 transition-colors"
                        >
                          <td className="py-3 px-4 text-muted-foreground text-sm">
                            {student.nisn || student.id}
                          </td>
                          <td className="py-3 px-4 text-foreground">{student.name}</td>
                          <td className="py-3 px-4 text-muted-foreground text-sm">{student.department}</td>
                          <td className="py-3 px-4 text-center text-foreground">{stats.total}</td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-3/10 text-chart-3 border border-chart-3/30">
                              <CheckCircle className="w-3.5 h-3.5" />
                              {stats.present}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-4/10 text-chart-4 border border-chart-4/30">
                              <Clock className="w-3.5 h-3.5" />
                              {stats.late}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-5/10 text-chart-5 border border-chart-5/30">
                              <XCircle className="w-3.5 h-3.5" />
                              {stats.absent}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="text-primary">{stats.rate}%</span>
                          </td>
                        </motion.tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            )
          ) : (
            filteredTeachers.length === 0 ? (
              <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
                <GraduationCap className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
                <p className="text-muted-foreground">Tidak ada data guru</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="border-b border-border">
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">Nama</th>
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">Mata Pelajaran</th>
                      <th className="text-left py-3 px-4 text-sm text-muted-foreground">Email</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Total</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Hadir</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Terlambat</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Tidak Hadir</th>
                      <th className="text-center py-3 px-4 text-sm text-muted-foreground">Tingkat Kehadiran</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredTeachers.map((teacher, index) => {
                      const stats = calculateStats(teacher.id);
                      return (
                        <motion.tr
                          key={teacher.id}
                          initial={{ opacity: 0, x: -20 }}
                          animate={{ opacity: 1, x: 0 }}
                          transition={{ duration: 0.3, delay: index * 0.03 }}
                          className="border-b border-border hover:bg-secondary/30 transition-colors"
                        >
                          <td className="py-3 px-4 text-foreground">{teacher.name}</td>
                          <td className="py-3 px-4 text-muted-foreground text-sm">{teacher.subject}</td>
                          <td className="py-3 px-4 text-muted-foreground text-sm">{teacher.email}</td>
                          <td className="py-3 px-4 text-center text-foreground">{stats.total}</td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-3/10 text-chart-3 border border-chart-3/30">
                              <CheckCircle className="w-3.5 h-3.5" />
                              {stats.present}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-4/10 text-chart-4 border border-chart-4/30">
                              <Clock className="w-3.5 h-3.5" />
                              {stats.late}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="inline-flex items-center gap-1 px-2 py-1 rounded-full text-sm bg-chart-5/10 text-chart-5 border border-chart-5/30">
                              <XCircle className="w-3.5 h-3.5" />
                              {stats.absent}
                            </span>
                          </td>
                          <td className="py-3 px-4 text-center">
                            <span className="text-primary">{stats.rate}%</span>
                          </td>
                        </motion.tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            )
          )}
        </div>
      </motion.div>
    </div>
  );
}
