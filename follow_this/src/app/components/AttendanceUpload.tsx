import { useState, useRef } from 'react';
import { Upload, FileSpreadsheet, CalendarCheck, CheckCircle2, AlertCircle, Download, ScanFace, CreditCard } from 'lucide-react';

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

interface AttendanceUploadProps {
  onUpload: (records: AttendanceRecord[]) => void;
  students: Student[];
}

export default function AttendanceUpload({ onUpload, students }: AttendanceUploadProps) {
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [parsedData, setParsedData] = useState<AttendanceRecord[]>([]);
  const [uploadStatus, setUploadStatus] = useState<'idle' | 'success' | 'error'>('idle');
  const [errorMessage, setErrorMessage] = useState('');
  const fileInputRef = useRef<HTMLInputElement>(null);

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
        const records: AttendanceRecord[] = [];

        for (let i = 1; i < lines.length; i++) {
          const values = lines[i].split(',').map(v => v.trim());

          const record: AttendanceRecord = {
            id: values[headers.indexOf('id')] || `ATT-${Date.now()}-${i}`,
            studentId: values[headers.indexOf('studentid')] || values[headers.indexOf('student_id')] || values[headers.indexOf('idsiswa')] || '',
            timestamp: values[headers.indexOf('timestamp')] || values[headers.indexOf('waktu')] || new Date().toISOString(),
            method: (values[headers.indexOf('method')] || values[headers.indexOf('metode')]) as 'face' | 'card' || 'card',
            status: (values[headers.indexOf('status')] as 'present' | 'late' | 'absent') || 'present',
            location: values[headers.indexOf('location')] || values[headers.indexOf('lokasi')] || 'Kampus Utama'
          };

          if (record.studentId) {
            records.push(record);
          }
        }

        setParsedData(records);
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
        if (fileInputRef.current) {
          fileInputRef.current.value = '';
        }
      }, 2000);
    }
  };

  const downloadTemplate = () => {
    const template = 'id,studentId,timestamp,method,status,location\nATT001,STU001,2026-04-14T09:00:00,face,present,Kampus Utama\nATT002,STU002,2026-04-14T09:05:00,card,late,Kampus Utama\nATT003,STU003,2026-04-14T09:00:00,face,present,Gedung A';
    const blob = new Blob([template], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_presensi.csv';
    a.click();
    URL.revokeObjectURL(url);
  };

  const getStudentName = (studentId: string) => {
    const student = students.find(s => s.id === studentId);
    return student?.name || studentId;
  };

  const faceRecords = parsedData.filter(r => r.method === 'face').length;
  const cardRecords = parsedData.filter(r => r.method === 'card').length;

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Header */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div className="flex items-start justify-between">
          <div>
            <h2 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>Unggah Data Presensi</h2>
            <p className="text-muted-foreground">Impor data presensi dari sistem pengenalan wajah dan akses kartu</p>
          </div>
          <button
            onClick={downloadTemplate}
            className="flex items-center gap-2 px-4 py-2 bg-secondary hover:bg-secondary/80 rounded-lg transition-colors border border-border"
          >
            <Download className="w-4 h-4" />
            Unduh Template
          </button>
        </div>
      </div>

      {/* Upload Area */}
      <div className="bg-card border border-border rounded-lg p-8 shadow-sm">
        <div
          onClick={() => fileInputRef.current?.click()}
          className="border-2 border-dashed border-border hover:border-primary rounded-lg p-12 text-center cursor-pointer transition-all hover:bg-primary/5"
        >
          <input
            ref={fileInputRef}
            type="file"
            accept=".csv"
            onChange={handleFileSelect}
            className="hidden"
          />

          <div className="flex justify-center mb-4">
            <div className="p-6 bg-primary/10 rounded-lg">
              <CalendarCheck className="w-12 h-12 text-primary" />
            </div>
          </div>

          <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
            {selectedFile ? selectedFile.name : 'Pilih file CSV atau seret dan lepas'}
          </h3>
          <p className="text-sm text-muted-foreground mb-4">
            File CSV dengan data presensi (id, studentId, timestamp, method, status, location)
          </p>

          {selectedFile && (
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-lg text-sm text-primary">
              <FileSpreadsheet className="w-4 h-4" />
              {selectedFile.name}
            </div>
          )}
        </div>
      </div>

      {/* Preview */}
      {parsedData.length > 0 && (
        <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-primary/10 rounded-lg">
                <CalendarCheck className="w-5 h-5 text-primary" />
              </div>
              <div>
                <h3 style={{ fontFamily: 'var(--font-display)' }}>Pratinjau</h3>
                <p className="text-sm text-muted-foreground">{parsedData.length} rekaman siap diunggah</p>
              </div>
            </div>

            <div className="flex items-center gap-4">
              <div className="flex items-center gap-2 px-3 py-1.5 bg-primary/10 rounded-lg border border-primary/20">
                <ScanFace className="w-4 h-4 text-primary" />
                <span className="text-sm">{faceRecords}</span>
              </div>
              <div className="flex items-center gap-2 px-3 py-1.5 bg-accent/10 rounded-lg border border-accent/20">
                <CreditCard className="w-4 h-4 text-accent" />
                <span className="text-sm">{cardRecords}</span>
              </div>
            </div>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-border">
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">ID Siswa</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Nama</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Waktu</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Metode</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Status</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Lokasi</th>
                </tr>
              </thead>
              <tbody>
                {parsedData.slice(0, 5).map((record) => (
                  <tr key={record.id} className="border-b border-border/50 last:border-0 hover:bg-secondary/50">
                    <td className="py-3 px-4 text-sm font-mono">{record.studentId}</td>
                    <td className="py-3 px-4">{getStudentName(record.studentId)}</td>
                    <td className="py-3 px-4 text-sm text-muted-foreground">
                      {new Date(record.timestamp).toLocaleString('id-ID')}
                    </td>
                    <td className="py-3 px-4">
                      <div className="flex items-center gap-2">
                        {record.method === 'face' ? (
                          <>
                            <ScanFace className="w-4 h-4 text-primary" />
                            <span className="text-sm text-primary">Wajah</span>
                          </>
                        ) : (
                          <>
                            <CreditCard className="w-4 h-4 text-accent" />
                            <span className="text-sm text-accent">Kartu</span>
                          </>
                        )}
                      </div>
                    </td>
                    <td className="py-3 px-4">
                      <span className={`px-2 py-1 rounded-md text-xs ${
                        record.status === 'present' ? 'bg-green-500/10 text-green-600 border border-green-500/20' :
                        record.status === 'late' ? 'bg-orange-500/10 text-orange-600 border border-orange-500/20' :
                        'bg-red-500/10 text-red-600 border border-red-500/20'
                      }`}>
                        {record.status === 'present' ? 'Hadir' : record.status === 'late' ? 'Terlambat' : 'Tidak Hadir'}
                      </span>
                    </td>
                    <td className="py-3 px-4 text-sm text-muted-foreground">{record.location}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {parsedData.length > 5 && (
            <p className="text-sm text-muted-foreground mt-4 text-center">
              ...dan {parsedData.length - 5} rekaman lainnya
            </p>
          )}

          <button
            onClick={handleUpload}
            disabled={uploadStatus === 'success'}
            className="w-full mt-6 flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
          >
            {uploadStatus === 'success' ? (
              <>
                <CheckCircle2 className="w-5 h-5" />
                Berhasil Diunggah!
              </>
            ) : (
              <>
                <Upload className="w-5 h-5" />
                Unggah {parsedData.length} Rekaman
              </>
            )}
          </button>
        </div>
      )}

      {/* Error Message */}
      {uploadStatus === 'error' && (
        <div className="bg-destructive/10 border border-destructive rounded-lg p-4 flex items-center gap-3">
          <AlertCircle className="w-5 h-5 text-destructive" />
          <p className="text-sm text-destructive">{errorMessage}</p>
        </div>
      )}

      {/* Instructions */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <h3 className="mb-4" style={{ fontFamily: 'var(--font-display)' }}>Persyaratan Format CSV</h3>
        <ul className="space-y-2 text-sm text-muted-foreground">
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Baris pertama harus berisi header: id, studentId, timestamp, method, status, location</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Kolom wajib: studentId (harus sesuai dengan ID siswa yang ada)</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Nilai method: "face" atau "card"</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Nilai status: "present", "late", atau "absent"</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Format timestamp: ISO 8601 (contoh: 2026-04-14T09:00:00) atau dibuat otomatis jika tidak diisi</span>
          </li>
        </ul>
      </div>
    </div>
  );
}
