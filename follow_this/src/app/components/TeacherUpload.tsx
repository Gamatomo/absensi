import { useState, useRef } from 'react';
import { Upload, FileSpreadsheet, GraduationCap, CheckCircle2, AlertCircle, Download } from 'lucide-react';

interface Teacher {
  id: string;
  name: string;
  email: string;
  cardId?: string;
  faceId?: string;
  subject: string;
  enrolledDate: string;
}

interface TeacherUploadProps {
  onUpload: (teachers: Teacher[]) => void;
}

export default function TeacherUpload({ onUpload }: TeacherUploadProps) {
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [parsedData, setParsedData] = useState<Teacher[]>([]);
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
        const teachers: Teacher[] = [];

        for (let i = 1; i < lines.length; i++) {
          const values = lines[i].split(',').map(v => v.trim());

          const teacher: Teacher = {
            id: values[headers.indexOf('id')] || `TCH-${Date.now()}-${i}`,
            name: values[headers.indexOf('name')] || values[headers.indexOf('nama')] || '',
            email: values[headers.indexOf('email')] || '',
            cardId: values[headers.indexOf('cardid')] || values[headers.indexOf('card_id')] || values[headers.indexOf('idkartu')],
            faceId: values[headers.indexOf('faceid')] || values[headers.indexOf('face_id')] || values[headers.indexOf('idwajah')],
            subject: values[headers.indexOf('subject')] || values[headers.indexOf('matapelajaran')] || values[headers.indexOf('mapel')] || 'Umum',
            enrolledDate: values[headers.indexOf('enrolleddate')] || values[headers.indexOf('enrolled_date')] || values[headers.indexOf('tanggalterdaftar')] || new Date().toISOString()
          };

          if (teacher.name && teacher.email) {
            teachers.push(teacher);
          }
        }

        setParsedData(teachers);
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
    const template = 'id,name,email,cardId,faceId,subject,enrolledDate\nTCH001,Dr. Ahmad Suryanto,ahmad.suryanto@example.com,CARD201,FACE301,Matematika,2026-01-15\nTCH002,Prof. Siti Rahmawati,siti.rahmawati@example.com,CARD202,FACE302,Fisika,2026-01-16';
    const blob = new Blob([template], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'template_data_guru.csv';
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <div className="max-w-4xl mx-auto space-y-6">
      {/* Header */}
      <div className="bg-card border border-border rounded-lg p-6 shadow-sm">
        <div className="flex items-start justify-between">
          <div>
            <h2 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>Unggah Data Guru</h2>
            <p className="text-muted-foreground">Impor informasi guru dari file CSV</p>
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
              <FileSpreadsheet className="w-12 h-12 text-primary" />
            </div>
          </div>

          <h3 className="mb-2" style={{ fontFamily: 'var(--font-display)' }}>
            {selectedFile ? selectedFile.name : 'Pilih file CSV atau seret dan lepas'}
          </h3>
          <p className="text-sm text-muted-foreground mb-4">
            File CSV dengan informasi guru (id, name, email, cardId, faceId, subject, enrolledDate)
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
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-primary/10 rounded-lg">
                <GraduationCap className="w-5 h-5 text-primary" />
              </div>
              <div>
                <h3 style={{ fontFamily: 'var(--font-display)' }}>Pratinjau</h3>
                <p className="text-sm text-muted-foreground">{parsedData.length} guru siap diunggah</p>
              </div>
            </div>
          </div>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-border">
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">ID</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Nama</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Email</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">Mata Pelajaran</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">ID Kartu</th>
                  <th className="text-left py-3 px-4 text-sm text-muted-foreground">ID Wajah</th>
                </tr>
              </thead>
              <tbody>
                {parsedData.slice(0, 5).map((teacher) => (
                  <tr key={teacher.id} className="border-b border-border/50 last:border-0 hover:bg-secondary/50">
                    <td className="py-3 px-4 text-sm">{teacher.id}</td>
                    <td className="py-3 px-4">{teacher.name}</td>
                    <td className="py-3 px-4 text-sm text-muted-foreground">{teacher.email}</td>
                    <td className="py-3 px-4 text-sm">{teacher.subject}</td>
                    <td className="py-3 px-4 text-sm font-mono text-primary">{teacher.cardId || '-'}</td>
                    <td className="py-3 px-4 text-sm font-mono text-accent">{teacher.faceId || '-'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {parsedData.length > 5 && (
            <p className="text-sm text-muted-foreground mt-4 text-center">
              ...dan {parsedData.length - 5} guru lainnya
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
                Unggah {parsedData.length} Guru
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
            <span>Baris pertama harus berisi header: id, name, email, cardId, faceId, subject, enrolledDate</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Kolom wajib: name, email</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Kolom opsional: id (dibuat otomatis jika tidak diisi), cardId, faceId, subject, enrolledDate</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="text-primary mt-0.5">•</span>
            <span>Gunakan koma (,) sebagai pemisah</span>
          </li>
        </ul>
      </div>
    </div>
  );
}
