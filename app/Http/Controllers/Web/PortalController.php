<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PortalDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    private const ROLE_MAP = [
        'admin' => 'admin',
        'teacher' => 'teacher',
        'guru' => 'teacher',
        'student' => 'student',
        'siswa' => 'student',
        'parent' => 'parent',
        'orang tua' => 'parent',
        'orang_tua' => 'parent',
        'orang_tua' => 'parent',
    ];

    public function __construct(private readonly PortalDataService $portalData) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $defaultRole = $this->normalizeRole($user->role);
        $previewRole = $this->normalizeRole($request->query('role', $defaultRole));
        $allowedRoles = array_values(self::ROLE_MAP);

        if (! in_array($previewRole, $allowedRoles, true)) {
            $previewRole = $defaultRole;
        }

        if ($defaultRole !== 'admin' && $previewRole !== $defaultRole) {
            $previewRole = $defaultRole;
        }

        return view('portal.index', array_merge(
            $this->portalData->payload(),
            [
                'userRole' => $previewRole,
                'roleLabels' => [
                    'admin' => 'Admin',
                    'student' => 'Siswa',
                    'teacher' => 'Guru',
                    'parent' => 'Orang Tua',
                ],
                'roleSubtitles' => [
                    'admin' => 'Pengelolaan Kehadiran Siswa',
                    'teacher' => 'Portal Guru',
                    'parent' => 'Portal Orang Tua',
                    'student' => 'Portal Siswa',
                ],
            ]
        ));
    }

    private function normalizeRole(?string $role): string
    {
        if (! is_string($role)) {
            return 'admin';
        }

        $normalized = strtolower(trim($role));

        return self::ROLE_MAP[$normalized] ?? 'admin';
    }
}
