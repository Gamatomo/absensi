import { useState } from 'react';
import { motion } from 'motion/react';
import {
  User,
  Mail,
  Building2,
  Calendar,
  IdCard,
  ScanFace,
  CreditCard,
  Edit,
  Save,
  X,
  Phone,
  MapPin
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
  phone?: string;
  address?: string;
}

interface StudentProfileProps {
  student: Student;
  onUpdate: (updatedStudent: Student) => void;
}

export default function StudentProfile({ student, onUpdate }: StudentProfileProps) {
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState(student);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onUpdate(formData);
    setIsEditing(false);
  };

  const handleCancel = () => {
    setFormData(student);
    setIsEditing(false);
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="flex items-center gap-2" style={{ fontFamily: 'var(--font-display)' }}>
            <User className="w-6 h-6 text-primary" />
            Profil Siswa
          </h2>
          <p className="text-sm text-muted-foreground mt-1">
            Kelola informasi profil Anda
          </p>
        </div>
        {!isEditing && (
          <button
            onClick={() => setIsEditing(true)}
            className="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
          >
            <Edit className="w-4 h-4" />
            Edit Profil
          </button>
        )}
      </div>

      {/* Profile Card */}
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
          {/* Header Section */}
          <div className="bg-gradient-to-r from-primary to-primary/80 px-8 py-12 text-center">
            <div className="flex justify-center mb-4">
              <div className="bg-primary-foreground/20 p-6 rounded-full backdrop-blur-sm border-4 border-primary-foreground/30">
                <User className="w-16 h-16 text-primary-foreground" />
              </div>
            </div>
            <h3 className="text-primary-foreground mb-1" style={{ fontFamily: 'var(--font-display)' }}>
              {student.name}
            </h3>
            <p className="text-primary-foreground/80 text-sm">
              {student.department}
            </p>
          </div>

          {/* Form Section */}
          <div className="p-8">
            {isEditing ? (
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="space-y-2">
                  <label htmlFor="name" className="block text-sm text-foreground">
                    Nama Lengkap
                  </label>
                  <div className="relative">
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                      <User className="w-5 h-5" />
                    </div>
                    <input
                      id="name"
                      type="text"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label htmlFor="email" className="block text-sm text-foreground">
                    Email
                  </label>
                  <div className="relative">
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                      <Mail className="w-5 h-5" />
                    </div>
                    <input
                      id="email"
                      type="email"
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label htmlFor="department" className="block text-sm text-foreground">
                    Jurusan
                  </label>
                  <div className="relative">
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                      <Building2 className="w-5 h-5" />
                    </div>
                    <input
                      id="department"
                      type="text"
                      value={formData.department}
                      onChange={(e) => setFormData({ ...formData, department: e.target.value })}
                      className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                      required
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label htmlFor="phone" className="block text-sm text-foreground">
                    Nomor Handphone
                  </label>
                  <div className="relative">
                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                      <Phone className="w-5 h-5" />
                    </div>
                    <input
                      id="phone"
                      type="tel"
                      value={formData.phone || ''}
                      onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
                      placeholder="08xxxxxxxxxx"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label htmlFor="address" className="block text-sm text-foreground">
                    Alamat Rumah
                  </label>
                  <div className="relative">
                    <div className="absolute left-3 top-3 text-muted-foreground">
                      <MapPin className="w-5 h-5" />
                    </div>
                    <textarea
                      id="address"
                      value={formData.address || ''}
                      onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                      rows={3}
                      className="w-full pl-11 pr-4 py-3 bg-input-background border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all resize-none"
                      placeholder="Alamat lengkap..."
                    />
                  </div>
                </div>

                <div className="flex gap-3">
                  <button
                    type="submit"
                    className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary hover:bg-primary/90 text-primary-foreground rounded-lg transition-all shadow-sm"
                  >
                    <Save className="w-4 h-4" />
                    Simpan Perubahan
                  </button>
                  <button
                    type="button"
                    onClick={handleCancel}
                    className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-secondary hover:bg-secondary/80 text-foreground rounded-lg transition-all border border-border"
                  >
                    <X className="w-4 h-4" />
                    Batal
                  </button>
                </div>
              </form>
            ) : (
              <div className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <Mail className="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground">Email</p>
                      <p className="text-foreground">{student.email}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <Building2 className="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground">Jurusan</p>
                      <p className="text-foreground">{student.department}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <IdCard className="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground">NIS/NISN</p>
                      <p className="text-foreground">{student.nisn || student.id}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <Calendar className="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground">Tanggal Daftar</p>
                      <p className="text-foreground">{formatDate(student.enrolledDate)}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <Phone className="w-5 h-5 text-primary" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground">Nomor Handphone</p>
                      <p className="text-foreground">{student.phone || 'Belum diisi'}</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-3 p-4 bg-secondary/30 rounded-lg border border-border md:col-span-2">
                    <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                      <MapPin className="w-5 h-5 text-primary" />
                    </div>
                    <div className="flex-1">
                      <p className="text-xs text-muted-foreground mb-1">Alamat Rumah</p>
                      <p className="text-foreground">{student.address || 'Belum diisi'}</p>
                    </div>
                  </div>
                </div>

                {/* Biometric Info */}
                <div className="mt-6 pt-6 border-t border-border">
                  <h4 className="mb-4 text-foreground" style={{ fontFamily: 'var(--font-display)' }}>
                    Informasi Biometrik
                  </h4>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                      <div className="bg-primary/10 p-2.5 rounded-lg border border-primary/20">
                        <ScanFace className="w-5 h-5 text-primary" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">ID Pengenalan Wajah</p>
                        <p className="text-foreground">{student.faceId || 'Belum terdaftar'}</p>
                      </div>
                    </div>

                    <div className="flex items-center gap-3 p-4 bg-secondary/30 rounded-lg border border-border">
                      <div className="bg-accent/10 p-2.5 rounded-lg border border-accent/20">
                        <CreditCard className="w-5 h-5 text-accent" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">ID Kartu</p>
                        <p className="text-foreground">{student.cardId || 'Belum terdaftar'}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </motion.div>
    </div>
  );
}
