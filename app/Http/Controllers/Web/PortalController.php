<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PortalDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(private readonly PortalDataService $portalData) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $allowedRoles = ['admin', 'student', 'teacher', 'parent'];
        $defaultRole = in_array($user->role, $allowedRoles, true) ? $user->role : 'admin';
        $previewRole = $request->query('role', $defaultRole);

        if (! in_array($previewRole, $allowedRoles, true)) {
            $previewRole = $defaultRole;
        }

        if ($user->role !== 'admin' && $previewRole !== $user->role) {
            $previewRole = $user->role;
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
}
