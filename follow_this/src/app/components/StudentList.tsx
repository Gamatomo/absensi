import { useState, useRef } from 'react';
import { Users, Search, ScanFace, CreditCard, Mail, Building2, Calendar, Upload, FileSpreadsheet, Download, CheckCircle2, AlertCircle, Phone, MapPin } from 'lucide-react';

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

interface StudentListProps {
  students: Student[];
  onUpload: (students: Student[]) => void;
}

export default function StudentList({ students, onUpload }: StudentListProps) {
  const [searchTerm, setSearchTerm] = useState('');
  const [filterDepartment, setFilterDepartment] = useState('all');
  const [showUpload, setShowUpload] = useState(false);
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [parsedData, setParsedData] = useState<Student[]>([]);
  const [uploadStatus, setUploadStatus] = useState<'idle' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');
  const fileInputRef = useRef<HTMLInputElement>(null);

  const departments = Array.from(new Set(students.map(s => s.department)));

  const handleFileSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      setSelectedFile(file);
      parseCSV(file);
    }
  };

  const parseCSV = (file: File) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const text = e.target?.result as string;
        const lines = text.split('\n').filter(line => line.trim());

        if (lines.length < 2) {
          throw new Error('File harus berisi header dan minimal satu baris data');
        }

        const headers = lines[0].split(',').map(h => h.trim().toLowerCase());
        const students: Student[] = [];

        for (let i = 1; i < lines.length; i++) {
          const values = lines[i].split(',').map(v => v.trim());

          const student: Student = {
            id: values[headers.indexOf('id')] || `STU-${Date.now()}-${i}`,
            name: values[headers.indexOf('name')] || values[headers.indexOf('nama')] || '',
            email: values[headers.indexOf('email')] || '',
            cardId: values[headers.indexOf('cardid')] || values[headers.indexOf('card_id')] || values[headers.indexOf('idkartu')],
            faceId: values[headers.indexOf('faceid')] || values[headers.indexOf('face_id')] || values[headers.indexOf('idwajah')],
            department: values[headers.indexOf('department')] || values[headers.indexOf('jurusan')] || 'Umum',
            enrolledDate: values[headers.indexOf('enrolleddate')] || values[headers.indexOf('enrolled_date')] || values[headers.indexOf('tanggalterdaftar')] || new Date().toISOString(),
            nisn: values[headers.indexOf('nisn')] || values[headers.indexOf('nis')],
            phone: values[headers.indexOf('phone')] || values[headers.indexOf('telephone')] || values[headers.indexOf('nomorhp')] || values[headers.indexOf('nomor_hp')],
            address: values[headers.indexOf('address')] || values[headers.indexOf('alamat')]
          };

          if (student.name && student.email) {
            students.push(student);
          }
        }

        setParsedData(students);
        setUploadStatus('idle');
        setErrorMessage('');
      } catch (error) {
        setErrorMessage(error instanceof Error ? error.message : 'Gagal membaca file CSV');
        setUploadStatus('error');
        setParsedData([]);
      }
    };
    reader.readAsText(file);
  };

  const handleUpload = () => {
    if (parsedData.length > 0) {
      onUpload(parsedData);
      setUploadStatus('success');
      setTimeout(() => {
        setUploadStatus('idle');
        setSelectedFile(null);
        setParsedData([]);
        setShowUpload(false);
        if (fileInputRef.current) {
          fileInputRef.current.value = '';
        }
      }, 2000);
    }
  };

  const downloadTemplate = () => {
    const template = 'id,name,email,nisn,cardId,faceId,department,enrolledDate,phone,address\nSTU001,Ahmad Rizki,ahmad@example.com,0012345678,CARD123,FACE456,Teknik Informatika,2026-01-15,081234567890,"Jl. Merdeka No. 123, Jakarta"';
    const blob = new Blob([template], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_data_siswa.csv';
    a.click();
    URL.revokeObjectURL(url);
  };

  const filteredStudents = students.filter(student => {
    const matchesSearch = student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         student.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         student.id.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesDepartment = filterDepartment === 'all' || student.department === filterDepartment;
    return matchesSearch && matchesDepartment;
  });

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="p-2.5 bg-primary/10 rounded-lg">
              <Users className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h2 style={{ fontFamily: 'var(--font-display)' }}>Data Siswa</h2>
              <p className="text-sm text-muted-foreground">{students.length} siswa terdaftar</p>
            </div>
          </div>
          <button
            onClick={() => setShowUpload(!showUpload)}
            className="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
          >
            <Upload className="w-4 h-4" />
            Unggah Data
          </button>
        </div>

        {/* Upload Form */}
        {showUpload && (
          <div className="mb-6 p-6 bg-secondary/30 rounded-lg border border-border">
            <h3 className="mb-4" style={{ fontFamily: 'var(--font-display)' }}>Unggah Data Siswa</h3>

            <div className="space-y-4">
              <div className="flex items-center gap-4">
                <div className="flex-1">
                  <input
                    ref={fileInputRef}
                    type="file"
                    accept=".csv"
                    onChange={handleFileSelect}
                    className="hidden"
                    id="student-file-upload"
                  />
                  <label
                    htmlFor="student-file-upload"
                    className="flex items-center gap-3 px-4 py-3 bg-card border-2 border-dashed border-border hover:border-primary rounded-lg cursor-pointer transition-all"
                  >
                    <FileSpreadsheet className="w-5 h-5 text-primary" />
                    <div className="flex-1">
                      <p className="text-sm">
                        {selectedFile ? selectedFile.name : 'Pilih file CSV'}
                      </p>
                      <p className="text-xs text-muted-foreground">Format: CSV dengan header</p>
                    </div>
                  </label>
                </div>
                <button
                  onClick={downloadTemplate}
                  className="flex items-center gap-2 px-4 py-3 bg-card hover:bg-secondary border border-border rounded-lg transition-all"
                  title="Unduh template CSV"
                >
                  <Download className="w-4 h-4" />
                  Template
                </button>
              </div>

              {parsedData.length > 0 && (
                <div className="p-4 bg-card rounded-lg border border-border">
                  <p className="text-sm mb-2">
                    <CheckCircle2 className="w-4 h-4 text-chart-3 inline mr-2" />
                    Berhasil memproses {parsedData.length} data siswa
                  </p>
                  <button
                    onClick={handleUpload}
                    className="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all"
                  >
                    <Upload className="w-4 h-4" />
                    Unggah {parsedData.length} Siswa
                  </button>
                </div>
              )}

              {uploadStatus === 'success' && (
                <div className="flex items-center gap-2 p-3 bg-chart-3/10 border border-chart-3/30 rounded-lg">
                  <CheckCircle2 className="w-5 h-5 text-chart-3" />
                  <span className="text-sm text-chart-3">Data siswa berhasil diunggah!</span>
                </div>
              )}

              {uploadStatus === 'error' && (
                <div className="flex items-center gap-2 p-3 bg-chart-5/10 border border-chart-5/30 rounded-lg">
                  <AlertCircle className="w-5 h-5 text-chart-5" />
                  <span className="text-sm text-chart-5">{errorMessage}</span>
                </div>
              )}
            </div>
          </div>
        )}

        <div className="flex flex-col sm:flex-row gap-4">
          {/* Search */}
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
            <input
              type="text"
              placeholder="Cari berdasarkan nama, email, atau ID..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-11 pr-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            />
          </div>

          {/* Department Filter */}
          <select
            value={filterDepartment}
            onChange={(e) => setFilterDepartment(e.target.value)}
            className="px-4 py-3 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all cursor-pointer"
          >
            <option value="all">Semua Jurusan</option>
            {departments.map(dept => (
              <option key={dept} value={dept}>{dept}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Student List */}
      {filteredStudents.length > 0 ? (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
          {filteredStudents.map((student) => (
            <div
              key={student.id}
              className="bg-card border border-border hover:border-primary/50 rounded-lg p-6 shadow-sm hover:shadow-md transition-all"
            >
              <div className="flex items-start justify-between mb-4">
                <div className="flex-1">
                  <h3 className="mb-1" style={{ fontFamily: 'var(--font-display)' }}>{student.name}</h3>
                  <p className="text-sm text-muted-foreground font-mono">{student.id}</p>
                </div>
                <div className="p-2 bg-primary/10 rounded-lg">
                  <Users className="w-5 h-5 text-primary" />
                </div>
              </div>

              <div className="space-y-3">
                <div className="flex items-center gap-3 text-sm">
                  <Mail className="w-4 h-4 text-muted-foreground" />
                  <span className="text-muted-foreground">{student.email}</span>
                </div>

                <div className="flex items-center gap-3 text-sm">
                  <Building2 className="w-4 h-4 text-muted-foreground" />
                  <span>{student.department}</span>
                </div>

                <div className="flex items-center gap-3 text-sm">
                  <Calendar className="w-4 h-4 text-muted-foreground" />
                  <span className="text-muted-foreground">
                    Terdaftar: {new Date(student.enrolledDate).toLocaleDateString('id-ID')}
                  </span>
                </div>
              </div>

              <div className="flex gap-2 mt-4 pt-4 border-t border-border">
                {student.faceId ? (
                  <div className="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-primary/10 rounded-md border border-primary/20">
                    <ScanFace className="w-4 h-4 text-primary" />
                    <span className="text-sm text-primary font-mono">{student.faceId}</span>
                  </div>
                ) : (
                  <div className="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-secondary rounded-md border border-border">
                    <ScanFace className="w-4 h-4 text-muted-foreground" />
                    <span className="text-sm text-muted-foreground">Belum Ada</span>
                  </div>
                )}

                {student.cardId ? (
                  <div className="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-accent/10 rounded-md border border-accent/20">
                    <CreditCard className="w-4 h-4 text-accent" />
                    <span className="text-sm text-accent font-mono">{student.cardId}</span>
                  </div>
                ) : (
                  <div className="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-secondary rounded-md border border-border">
                    <CreditCard className="w-4 h-4 text-muted-foreground" />
                    <span className="text-sm text-muted-foreground">Belum Ada</span>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="bg-card border border-border rounded-lg p-12 shadow-sm text-center">
          <div className="inline-flex p-6 bg-secondary rounded-lg mb-4">
            <Users className="w-12 h-12 text-muted-foreground" />
          </div>
          <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
            {students.length === 0 ? 'Belum Ada Data Siswa' : 'Tidak Ada Hasil'}
          </h3>
          <p className="text-muted-foreground">
            {students.length === 0
              ? 'Unggah data siswa untuk memulai'
              : 'Coba sesuaikan pencarian atau filter Anda'}
          </p>
        </div>
      )}
    </div>
  );
}
