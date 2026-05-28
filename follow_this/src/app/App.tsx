import { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@radix-ui/react-tabs';
import {
  Users,
  CalendarCheck,
  Upload,
  TrendingUp,
  ScanFace,
  CreditCard,
  BarChart3,
  UserPlus,
  FileSpreadsheet,
  GraduationCap,
  User as UserIcon,
  Home,
  FileText,
  User,
  ClipboardList,
  CheckCircle
} from 'lucide-react';
import { motion } from 'motion/react';
import Dashboard from './components/Dashboard';
import StudentDashboard from './components/StudentDashboard';
import LeaveRequestForm from './components/LeaveRequestForm';
import StudentProfile from './components/StudentProfile';
import AttendanceRecap from './components/AttendanceRecap';
import StudentList from './components/StudentList';
import TeacherList from './components/TeacherList';
import AdminAttendanceRecap from './components/AdminAttendanceRecap';
import LeaveRequestManagement from './components/LeaveRequestManagement';
import TeacherProfile from './components/TeacherProfile';
import TeacherDashboard from './components/TeacherDashboard';
import TeacherAttendanceRecap from './components/TeacherAttendanceRecap';
import TeacherLeaveRequestForm from './components/TeacherLeaveRequestForm';

interface Student {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  department: string;
  enrolledDate: string;
  nisn?: string;
  phone?: string;
  address?: string;
}

interface Teacher {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  subject: string;
  enrolledDate: string;
  phone?: string;
  address?: string;
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

export default function App() {
  // Mock current user - in production, this would come from authentication
  const [userRole, setUserRole] = useState<'admin' | 'student' | 'teacher'>('teacher');
  const [currentStudentId, setCurrentStudentId] = useState<string>('STU001');
  const [currentTeacherId, setCurrentTeacherId] = useState<string>('TCH001');
  const [studentTab, setStudentTab] = useState<'dashboard' | 'recap' | 'leave' | 'profile'>('dashboard');
  const [teacherTab, setTeacherTab] = useState<'dashboard' | 'recap' | 'leave' | 'profile'>('dashboard');

  const [students, setStudents] = useState<Student[]>([
    {
      id: 'STU001',
      name: 'Ahmad Fauzi',
      email: 'ahmad.fauzi@sekolah.ac.id',
      cardId: 'CARD-001',
      faceId: 'FACE-001',
      department: 'Teknik Alat Berat',
      enrolledDate: '2024-01-15',
      nisn: '0012345678',
      phone: '081234567890',
      address: 'Jl. Merdeka No. 123, Jakarta Pusat'
    }
  ]);
  const [teachers, setTeachers] = useState<Teacher[]>([
    {
      id: 'TCH001',
      name: 'Budi Santoso',
      email: 'budi.santoso@sekolah.ac.id',
      cardId: 'CARD-T001',
      faceId: 'FACE-T001',
      subject: 'Matematika',
      enrolledDate: '2023-08-01',
      phone: '082345678901',
      address: 'Jl. Sudirman No. 45, Jakarta Selatan'
    }
  ]);
  const [attendanceRecords, setAttendanceRecords] = useState<AttendanceRecord[]>([
    {
      id: 'ATT001',
      studentId: 'STU001',
      timestamp: '2026-05-06T07:30:00',
      method: 'face',
      status: 'present',
      location: 'Gedung A - Lantai 1'
    },
    {
      id: 'ATT002',
      studentId: 'STU001',
      timestamp: '2026-05-05T07:45:00',
      method: 'card',
      status: 'late',
      location: 'Gedung A - Lantai 1'
    },
    {
      id: 'ATT003',
      studentId: 'STU001',
      timestamp: '2026-05-04T07:25:00',
      method: 'face',
      status: 'present',
      location: 'Gedung A - Lantai 1'
    }
  ]);
  const [leaveRequests, setLeaveRequests] = useState<LeaveRequest[]>([
    {
      id: 'LR001',
      studentId: 'STU001',
      reason: 'Sakit',
      startDate: '2026-05-01',
      endDate: '2026-05-02',
      description: 'Demam dan flu, perlu istirahat di rumah',
      status: 'approved',
      submittedAt: '2026-04-30T10:00:00'
    }
  ]);
  const [activeTab, setActiveTab] = useState('dashboard');

  const handleStudentUpload = (newStudents: Student[]) => {
    setStudents(prev => [...prev, ...newStudents]);
  };

  const handleTeacherUpload = (newTeachers: Teacher[]) => {
    setTeachers(prev => [...prev, ...newTeachers]);
  };

  const handleAttendanceUpload = (newRecords: AttendanceRecord[]) => {
    setAttendanceRecords(prev => [...prev, ...newRecords]);
  };

  const handleLeaveRequest = (request: Omit<LeaveRequest, 'id' | 'status' | 'submittedAt'>) => {
    const newRequest: LeaveRequest = {
      ...request,
      id: `LR${String(leaveRequests.length + 1).padStart(3, '0')}`,
      status: 'pending',
      submittedAt: new Date().toISOString()
    };
    setLeaveRequests(prev => [...prev, newRequest]);
  };

  const handleUpdateLeaveStatus = (requestId: string, status: 'approved' | 'rejected') => {
    setLeaveRequests(prev => prev.map(req =>
      req.id === requestId ? { ...req, status } : req
    ));
  };

  const handleStudentUpdate = (updatedStudent: Student) => {
    setStudents(prev => prev.map(s => s.id === updatedStudent.id ? updatedStudent : s));
  };

  const handleTeacherUpdate = (updatedTeacher: Teacher) => {
    setTeachers(prev => prev.map(t => t.id === updatedTeacher.id ? updatedTeacher : t));
  };

  const handleAttendanceSubmit = (record: Omit<AttendanceRecord, 'id'>) => {
    const newRecord: AttendanceRecord = {
      ...record,
      id: `ATT${String(attendanceRecords.length + 1).padStart(3, '0')}`
    };
    setAttendanceRecords(prev => [...prev, newRecord]);
  };

  const currentStudent = students.find(s => s.id === currentStudentId);
  const currentTeacher = teachers.find(t => t.id === currentTeacherId);
  const studentLeaveRequests = leaveRequests.filter(r => r.studentId === currentStudentId);
  const teacherLeaveRequests = leaveRequests.filter(r => r.studentId === currentTeacherId);

  return (
    <div className="min-h-screen bg-background">
      <div>
        {/* Header */}
        <header className="border-b border-border bg-card shadow-sm">
          <div className="container mx-auto px-6 py-5">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <div className="bg-primary p-3 rounded-lg">
                  <CalendarCheck className="w-8 h-8 text-primary-foreground" />
                </div>
                <div>
                  <h1 className="text-2xl tracking-tight" style={{ fontFamily: 'var(--font-display)' }}>
                    Sistem Presensi
                  </h1>
                  <p className="text-sm text-muted-foreground">
                    {userRole === 'admin' ? 'Pengelolaan Kehadiran Siswa' : userRole === 'teacher' ? 'Portal Guru' : 'Portal Siswa'}
                  </p>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <div className="flex items-center gap-3 bg-secondary px-4 py-2 rounded-lg border border-border">
                  <div className="flex items-center gap-2 px-3 py-1.5 bg-background rounded-md border border-border">
                    <ScanFace className="w-4 h-4 text-primary" />
                    <span className="text-sm text-foreground">Pengenalan Wajah</span>
                  </div>
                  <div className="flex items-center gap-2 px-3 py-1.5 bg-background rounded-md border border-border">
                    <CreditCard className="w-4 h-4 text-accent" />
                    <span className="text-sm text-foreground">Akses Kartu</span>
                  </div>
                </div>
                <button
                  onClick={() => {
                    if (userRole === 'admin') setUserRole('student');
                    else if (userRole === 'student') setUserRole('teacher');
                    else setUserRole('admin');
                  }}
                  className="flex items-center gap-2 px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary rounded-lg border border-primary/30 transition-all"
                >
                  <UserIcon className="w-4 h-4" />
                  <span className="text-sm">
                    {userRole === 'admin' ? 'Admin' : userRole === 'teacher' ? 'Guru' : 'Siswa'}
                  </span>
                </button>
              </div>
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="container mx-auto px-6 py-8">
          {userRole === 'student' && currentStudent ? (
            <div>
              {/* Student Navigation */}
              <div className="mb-8">
                <div className="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
                  <button
                    onClick={() => setStudentTab('dashboard')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      studentTab === 'dashboard'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <Home className="w-4 h-4" />
                    Beranda
                  </button>
                  <button
                    onClick={() => setStudentTab('recap')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      studentTab === 'recap'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <ClipboardList className="w-4 h-4" />
                    Rekap Absensi
                  </button>
                  <button
                    onClick={() => setStudentTab('leave')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      studentTab === 'leave'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <FileText className="w-4 h-4" />
                    Pengajuan Izin
                  </button>
                  <button
                    onClick={() => setStudentTab('profile')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      studentTab === 'profile'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <User className="w-4 h-4" />
                    Profil
                  </button>
                </div>
              </div>

              {/* Student Content */}
              {studentTab === 'dashboard' && (
                <StudentDashboard student={currentStudent} attendanceRecords={attendanceRecords} />
              )}
              {studentTab === 'recap' && (
                <AttendanceRecap student={currentStudent} attendanceRecords={attendanceRecords} />
              )}
              {studentTab === 'leave' && (
                <LeaveRequestForm
                  student={currentStudent}
                  onSubmit={handleLeaveRequest}
                  requests={studentLeaveRequests}
                />
              )}
              {studentTab === 'profile' && (
                <StudentProfile student={currentStudent} onUpdate={handleStudentUpdate} />
              )}
            </div>
          ) : userRole === 'teacher' && currentTeacher ? (
            <div>
              {/* Teacher Navigation */}
              <div className="mb-8">
                <div className="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border">
                  <button
                    onClick={() => setTeacherTab('dashboard')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      teacherTab === 'dashboard'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <Home className="w-4 h-4" />
                    Beranda
                  </button>
                  <button
                    onClick={() => setTeacherTab('recap')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      teacherTab === 'recap'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <ClipboardList className="w-4 h-4" />
                    Rekap Absensi
                  </button>
                  <button
                    onClick={() => setTeacherTab('leave')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      teacherTab === 'leave'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <FileText className="w-4 h-4" />
                    Pengajuan Cuti
                  </button>
                  <button
                    onClick={() => setTeacherTab('profile')}
                    className={`inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all ${
                      teacherTab === 'profile'
                        ? 'bg-primary text-primary-foreground shadow-sm'
                        : 'hover:bg-secondary/80'
                    }`}
                  >
                    <User className="w-4 h-4" />
                    Profil
                  </button>
                </div>
              </div>

              {/* Teacher Content */}
              {teacherTab === 'dashboard' && (
                <TeacherDashboard teacher={currentTeacher} attendanceRecords={attendanceRecords} />
              )}
              {teacherTab === 'recap' && (
                <TeacherAttendanceRecap teacher={currentTeacher} attendanceRecords={attendanceRecords} />
              )}
              {teacherTab === 'leave' && (
                <TeacherLeaveRequestForm
                  teacher={currentTeacher}
                  onSubmit={handleLeaveRequest}
                  requests={teacherLeaveRequests}
                />
              )}
              {teacherTab === 'profile' && (
                <TeacherProfile teacher={currentTeacher} onUpdate={handleTeacherUpdate} />
              )}
            </div>
          ) : (
            <Tabs value={activeTab} onValueChange={setActiveTab}>
              <div>
                <TabsList className="inline-flex h-11 items-center justify-center rounded-lg bg-secondary p-1 text-muted-foreground border border-border mb-8">
                <TabsTrigger
                  value="dashboard"
                  className="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm"
                >
                  <BarChart3 className="w-4 h-4" />
                  Beranda
                </TabsTrigger>
                <TabsTrigger
                  value="students"
                  className="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm"
                >
                  <Users className="w-4 h-4" />
                  Data Siswa
                </TabsTrigger>
                <TabsTrigger
                  value="teachers"
                  className="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm"
                >
                  <GraduationCap className="w-4 h-4" />
                  Data Guru
                </TabsTrigger>
                <TabsTrigger
                  value="recap"
                  className="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm"
                >
                  <FileSpreadsheet className="w-4 h-4" />
                  Rekap Absensi
                </TabsTrigger>
                <TabsTrigger
                  value="leave-requests"
                  className="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 gap-2 transition-all data-[state=active]:bg-primary data-[state=active]:text-primary-foreground data-[state=active]:shadow-sm"
                >
                  <FileText className="w-4 h-4" />
                  Kelola Izin/Cuti
                </TabsTrigger>
              </TabsList>
            </div>

              <TabsContent value="dashboard" className="mt-0">
                <Dashboard students={students} teachers={teachers} attendanceRecords={attendanceRecords} leaveRequests={leaveRequests} />
              </TabsContent>

              <TabsContent value="students" className="mt-0">
                <StudentList students={students} onUpload={handleStudentUpload} />
              </TabsContent>

              <TabsContent value="teachers" className="mt-0">
                <TeacherList teachers={teachers} onUpload={handleTeacherUpload} />
              </TabsContent>

              <TabsContent value="recap" className="mt-0">
                <AdminAttendanceRecap students={students} teachers={teachers} attendanceRecords={attendanceRecords} />
              </TabsContent>

              <TabsContent value="leave-requests" className="mt-0">
                <LeaveRequestManagement
                  leaveRequests={leaveRequests}
                  students={students}
                  teachers={teachers}
                  onUpdateStatus={handleUpdateLeaveStatus}
                />
              </TabsContent>
            </Tabs>
          )}
        </main>
      </div>
    </div>
  );
}