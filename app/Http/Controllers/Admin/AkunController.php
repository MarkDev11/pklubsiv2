<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\StoreAkunRequest;
use App\Http\Requests\Admin\UpdateAkunRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): View
    {
        return view('admin.akun.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $keyword = trim($request->input('search.value', '') ?? '');
        $orderCol = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'asc');

        $colMap = [0 => null, 1 => 'username', 2 => 'name', 3 => 'role', 4 => null];
        $orderBy = $colMap[$orderCol] ?? 'username';

        $total = User::count();

        $query = User::query();

        $roleFilter = null;
        $roles = ['mahasiswa', 'dosen', 'mentor', 'admin'];
        foreach ($roles as $role) {
            if (str_contains($keyword, $role)) {
                $roleFilter = $role;
                $keyword = trim(str_replace($role, '', $keyword));
                break;
            }
        }

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('username', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('kd_lokal', 'like', "%{$keyword}%");
            });
        }

        $filtered = $query->count();

        $rows = $query->orderBy($orderBy, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = [
                'no' => $start + $i + 1,
                'username' => e($r->username),
                'name' => e($r->name),
                'role' => $r->role,
                'jenis' => $r->jenis,
                'dosen_pa' => $r->nama_dosen_pa,
                'kd_lokal' => $r->kd_lokal,
                'encrypted_id' => encryptUrl($r->username),
                'is_admin' => $r->isAdmin(),
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function create(): View
    {
        return view('admin.akun.create');
    }

    public function store(StoreAkunRequest $request): RedirectResponse
    {
        $user = $this->userService->createAccount($request->validated());

        ActivityLog::log(Auth::user()?->id, 'Menambahkan akun manual: '.$user->username);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(string $encrypted): View
    {
        $username = decryptUrl($encrypted);
        $user = User::where('username', $username)->firstOrFail();

        return view('admin.akun.edit', compact('user'));
    }

    public function update(UpdateAkunRequest $request, string $encrypted): RedirectResponse
    {
        $username = decryptUrl($encrypted);
        $user = User::where('username', $username)->firstOrFail();

        $this->authorize('update', $user);

        $this->userService->updateAccount($user, $request->validated());

        ActivityLog::log(Auth::user()?->id, 'Update akun '.$user->username);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(string $encrypted): RedirectResponse
    {
        $username = decryptUrl($encrypted);
        $user = User::where('username', $username)->firstOrFail();

        $this->authorize('delete', $user);

        try {
            $this->userService->deleteAccount($user);
            ActivityLog::log(Auth::user()?->id, 'Hapus akun: '.$user->username);

            return redirect()->route('admin.akun.index')
                ->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetPassword(ResetPasswordRequest $request, string $encrypted): RedirectResponse
    {
        $username = decryptUrl($encrypted);
        $user = User::where('username', $username)->firstOrFail();

        $this->authorize('resetPassword', $user);

        $this->userService->resetPassword($user, $request->validated()['password']);

        ActivityLog::log(Auth::user()?->id, 'Reset password akun: '.$user->username);

        return redirect()->route('admin.akun.index')
            ->with('success', 'Password berhasil direset.');
    }

    public function log(string $encrypted): View
    {
        $username = decryptUrl($encrypted);
        $user = User::where('username', $username)->firstOrFail();
        $logs = ActivityLog::where('user_id', $user->id)->orderBy('waktu', 'desc')->paginate(50);

        return view('admin.akun.log', compact('user', 'logs'));
    }
}
