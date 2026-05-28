import { motion } from 'motion/react';
import { Users, CalendarCheck, TrendingUp, Clock, GraduationCap, FileText } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

interface Student {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  department: string;
  enrolledDate: string;
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

interface DashboardProps {
  students: Student[];
  teachers: Teacher[];
  attendanceRecords: AttendanceRecord[];
  leaveRequests: LeaveRequest[];
}

export default function Dashboard({ students, teachers, attendanceRecords, leaveRequests }: DashboardProps) {
  const totalStudents = students.length;
  const totalTeachers = teachers.length;
  const totalLeaveRequests = leaveRequests.length;
  const todayRecords = attendanceRecords.filter(record => {
    const recordDate = new Date(record.timestamp);
    const today = new Date();
    return recordDate.toDateString() === today.toDateString();
  });

  const presentToday = todayRecords.filter(r => r.status === 'present').length;
  const attendanceRate = totalStudents > 0 ? ((presentToday / totalStudents) * 100).toFixed(1) : '0';

  const weeklyData = Array.from({ length: 7 }, (_, i) => {
    const date = new Date();
    date.setDate(date.getDate() - (6 - i));
    const dayRecords = attendanceRecords.filter(record => {
      const recordDate = new Date(record.timestamp);
      return recordDate.toDateString() === date.toDateString();
    });
    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const uniqueKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    return {
      id: uniqueKey,
      day: `${days[date.getDay()]} ${date.getDate()}/${date.getMonth() + 1}`,
      present: dayRecords.filter(r => r.status === 'present').length,
      late: dayRecords.filter(r => r.status === 'late').length,
      absent: dayRecords.filter(r => r.status === 'absent').length
    };
  });

  return (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-primary/10 rounded-lg">
              <Users className="w-5 h-5 text-primary" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{totalStudents}</h3>
          <p className="text-sm text-muted-foreground">Total Siswa</p>
        </div>

        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-purple-500/10 rounded-lg">
              <GraduationCap className="w-5 h-5 text-purple-600" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{totalTeachers}</h3>
          <p className="text-sm text-muted-foreground">Total Guru</p>
        </div>

        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-green-500/10 rounded-lg">
              <CalendarCheck className="w-5 h-5 text-green-600" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{presentToday}</h3>
          <p className="text-sm text-muted-foreground">Hadir Hari Ini</p>
        </div>

        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-blue-500/10 rounded-lg">
              <TrendingUp className="w-5 h-5 text-blue-600" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{attendanceRate}%</h3>
          <p className="text-sm text-muted-foreground">Tingkat Kehadiran</p>
        </div>

        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-slate-500/10 rounded-lg">
              <Clock className="w-5 h-5 text-slate-600" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{attendanceRecords.length}</h3>
          <p className="text-sm text-muted-foreground">Total Rekaman</p>
        </div>

        <div className="bg-card border border-border rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
          <div className="flex items-start justify-between mb-4">
            <div className="p-2.5 bg-orange-500/10 rounded-lg">
              <FileText className="w-5 h-5 text-orange-600" />
            </div>
          </div>
          <h3 className="text-3xl mb-1" style={{ fontFamily: 'var(--font-display)' }}>{totalLeaveRequests}</h3>
          <p className="text-sm text-muted-foreground">Total Izin</p>
        </div>
      </div>

      {/* Chart */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h3 className="mb-6" style={{ fontFamily: 'var(--font-display)' }}>Kehadiran Mingguan</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={weeklyData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" />
            <XAxis dataKey="day" stroke="#64748b" style={{ fontSize: '14px' }} />
            <YAxis stroke="#64748b" style={{ fontSize: '14px' }} />
            <Tooltip
              contentStyle={{
                backgroundColor: '#ffffff',
                border: '1px solid #e2e8f0',
                borderRadius: '0.5rem',
                boxShadow: '0 1px 3px 0 rgb(0 0 0 / 0.1)'
              }}
            />
            <Bar dataKey="present" fill="#1e3a8a" radius={[4, 4, 0, 0]} name="Hadir" />
            <Bar dataKey="late" fill="#f59e0b" radius={[4, 4, 0, 0]} name="Terlambat" />
            <Bar dataKey="absent" fill="#dc2626" radius={[4, 4, 0, 0]} name="Tidak Hadir" />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}