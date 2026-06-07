import { useState } from 'react';
import { motion } from 'motion/react';
import {
  FileText,
  Search,
  Filter,
  CheckCircle,
  XCircle,
  Clock,
  User,
  Calendar,
  Users,
  GraduationCap
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

interface LeaveRequestManagementProps {
  leaveRequests: LeaveRequest[];
  students: Student[];
  teachers: Teacher[];
  onUpdateStatus: (requestId: string, status: 'approved' | 'rejected') => void;
}

export default function LeaveRequestManagement({
  leaveRequests,
  students,
  teachers,
  onUpdateStatus
}: LeaveRequestManagementProps) {
  const [activeView, setActiveView] = useState<'students' | 'teachers'>('students');
  const [searchQuery, setSearchQuery] = useState('');
  const [filterStatus, setFilterStatus] = useState<string>('all');

  const getStudent = (studentId: string) => {
    return students.find(s => s.id === studentId);
  };

  const getTeacher = (teacherId: string) => {
    return teachers.find(t => t.id === teacherId);
  };

  // Filter requests by person type
  const studentIds = students.map(s => s.id);
  const teacherIds = teachers.map(t => t.id);

  const currentRequests = leaveRequests.filter(request =>
    activeView === 'students'
      ? studentIds.includes(request.studentId)
      : teacherIds.includes(request.studentId)
  );

  const filteredRequests = currentRequests.filter(request => {
    const person = activeView === 'students'
      ? getStudent(request.studentId)
      : getTeacher(request.studentId);
    const matchesSearch = person?.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         request.reason.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesStatus = filterStatus === 'all' || request.status === filterStatus;
    return matchesSearch && matchesStatus;
  });

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
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

  const pendingCount = currentRequests.filter(r => r.status === 'pending').length;
  const approvedCount = currentRequests.filter(r => r.status === 'approved').length;
  const rejectedCount = currentRequests.filter(r => r.status === 'rejected').length;

  return (
    <div className="space-y-6">
      {/* Header & Stats */}
      <div>
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 style={{ fontFamily: 'var(--font-display)' }}>
              Kelola Izin/Cuti
            </h2>
            <p className="text-sm text-muted-foreground mt-1">
              Kelola permohonan izin siswa dan cuti guru
            </p>
          </div>
        </div>

        {/* View Toggle */}
        <div className="flex items-center gap-3 mb-6">
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
              Izin Siswa
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
              Cuti Guru
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <div className="bg-card border border-border rounded-lg p-4 shadow-sm">
            <div className="flex items-center gap-3">
              <div className="p-2.5 bg-chart-4/10 rounded-lg">
                <Clock className="w-5 h-5 text-chart-4" />
              </div>
              <div>
                <p className="text-sm text-muted-foreground">Menunggu</p>
                <h3 className="text-2xl text-chart-4" style={{ fontFamily: 'var(--font-display)' }}>
                  {pendingCount}
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
                <p className="text-sm text-muted-foreground">Disetujui</p>
                <h3 className="text-2xl text-chart-3" style={{ fontFamily: 'var(--font-display)' }}>
                  {approvedCount}
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
                <p className="text-sm text-muted-foreground">Ditolak</p>
                <h3 className="text-2xl text-chart-5" style={{ fontFamily: 'var(--font-display)' }}>
                  {rejectedCount}
                </h3>
              </div>
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
                  placeholder={`Cari ${activeView === 'students' ? 'siswa atau alasan izin' : 'guru atau alasan cuti'}...`}
                  className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                />
              </div>
            </div>
            <div className="space-y-2">
              <label htmlFor="filterStatus" className="block text-sm text-foreground">
                Status Permohonan
              </label>
              <select
                id="filterStatus"
                value={filterStatus}
                onChange={(e) => setFilterStatus(e.target.value)}
                className="w-full px-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              >
                <option value="all">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
              </select>
            </div>
          </div>
        </div>
      </motion.div>

      {/* Requests List */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, delay: 0.1 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm p-6">
          <h3 className="mb-4 flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <FileText className="w-5 h-5 text-primary" />
            Daftar {activeView === 'students' ? 'Izin Siswa' : 'Cuti Guru'}
          </h3>

          {filteredRequests.length === 0 ? (
            <div className="text-center py-12 bg-secondary/30 rounded-lg border border-dashed border-border">
              <FileText className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
              <p className="text-muted-foreground">Tidak ada permohonan {activeView === 'students' ? 'izin' : 'cuti'}</p>
            </div>
          ) : (
            <div className="space-y-4">
              {filteredRequests
                .sort((a, b) => new Date(b.submittedAt).getTime() - new Date(a.submittedAt).getTime())
                .map((request, index) => {
                  const person = activeView === 'students'
                    ? getStudent(request.studentId)
                    : getTeacher(request.studentId);
                  return (
                    <motion.div
                      key={request.id}
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ duration: 0.3, delay: index * 0.05 }}
                      className="p-5 bg-secondary/30 rounded-lg border border-border hover:bg-secondary/50 transition-colors"
                    >
                      <div className="flex items-start justify-between mb-4">
                        <div className="flex items-start gap-4 flex-1">
                          <div className="p-3 bg-primary/10 rounded-lg border border-primary/20">
                            <User className="w-5 h-5 text-primary" />
                          </div>
                          <div className="flex-1">
                            <h4 className="text-foreground mb-1">
                              {person?.name || (activeView === 'students' ? 'Siswa Tidak Ditemukan' : 'Guru Tidak Ditemukan')}
                            </h4>
                            <p className="text-sm text-muted-foreground mb-2">
                              {activeView === 'students'
                                ? `${(person as Student)?.department} • NIS/NISN: ${(person as Student)?.nisn || person?.id}`
                                : `${(person as Teacher)?.subject} • ID: ${person?.id}`
                              }
                            </p>
                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                              <Calendar className="w-4 h-4" />
                              <span>
                                {formatDate(request.startDate)} - {formatDate(request.endDate)}
                              </span>
                            </div>
                          </div>
                        </div>
                        <span className={`px-3 py-1 rounded-full text-sm border ${getStatusColor(request.status)}`}>
                          {getStatusText(request.status)}
                        </span>
                      </div>

                      <div className="mb-4 p-4 bg-background rounded-lg border border-border">
                        <p className="text-sm text-muted-foreground mb-1">{activeView === 'students' ? 'Alasan Izin:' : 'Alasan Cuti:'}</p>
                        <p className="text-foreground mb-3">{request.reason}</p>
                        <p className="text-sm text-muted-foreground mb-1">Keterangan:</p>
                        <p className="text-sm text-muted-foreground">{request.description}</p>
                      </div>

                      <div className="flex items-center justify-between">
                        <p className="text-xs text-muted-foreground">
                          Diajukan: {formatDate(request.submittedAt)}
                        </p>
                        {request.status === 'pending' && (
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => onUpdateStatus(request.id, 'rejected')}
                              className="flex items-center gap-2 px-4 py-2 bg-chart-5/10 hover:bg-chart-5/20 text-chart-5 rounded-lg border border-chart-5/30 transition-all"
                            >
                              <XCircle className="w-4 h-4" />
                              Tolak
                            </button>
                            <button
                              onClick={() => onUpdateStatus(request.id, 'approved')}
                              className="flex items-center gap-2 px-4 py-2 bg-chart-3/10 hover:bg-chart-3/20 text-chart-3 rounded-lg border border-chart-3/30 transition-all"
                            >
                              <CheckCircle className="w-4 h-4" />
                              Setujui
                            </button>
                          </div>
                        )}
                      </div>
                    </motion.div>
                  );
                })}
            </div>
          )}
        </div>
      </motion.div>
    </div>
  );
}
