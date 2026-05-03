<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Address;

class ProfilePage extends Component
{
    use WithFileUploads;

    public string $activeTab = 'profile';

    // ── Profile
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $gender   = '';
    public string $birthday = '';
    public string $bio      = '';
    public $avatar;

    // ── Password
    public string $current_password          = '';
    public string $new_password              = '';
    public string $new_password_confirmation = '';

    // ── Orders
    public string $orderStatus = '';

    // ── Address
    public bool   $showAddressModal = false;
    public ?int   $editingId        = null;
    public string $receiverName     = '';
    public string $receiverPhone    = '';
    public string $provinceName     = '';
    public string $provinceCode     = '';
    public string $wardName         = '';
    public string $wardCode         = '';
    public string $addressLine      = '';
    public bool   $isDefault        = false;

    public function mount(): void
    {
        $this->activeTab   = request('tab', 'profile');
        $this->orderStatus = request('order_status', '');

        $user = Auth::user();
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->phone    = $user->phone    ?? '';
        $this->gender   = $user->gender   ?? '';
        $this->birthday = $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '';
        $this->bio      = $user->bio      ?? '';
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetValidation();
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    // ── Profile ─────────────────────────────────────────────
    public function saveProfile(): void
    {
        $user = Auth::user();
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'gender'   => ['nullable', 'in:male,female,other'],
            'birthday' => ['nullable', 'date'],
            'bio'      => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone    ?: null,
            'gender'   => $this->gender   ?: null,
            'birthday' => $this->birthday ?: null,
            'bio'      => $this->bio      ?: null,
        ]);

        if ($this->avatar) {
            $this->validate(['avatar' => ['image', 'max:2048']]);
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => $this->avatar->store('avatars', 'public')]);
            $this->reset('avatar');
        }

        $this->dispatch('notify', type: 'success', message: 'Đã cập nhật thông tin!');
    }

    // ── Password ─────────────────────────────────────────────
    public function changePassword(): void
    {
        $this->validate([
            'current_password'          => ['required'],
            'new_password'              => ['required', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required'],
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'Mật khẩu hiện tại không đúng.');
            return;
        }

        Auth::user()->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('notify', type: 'success', message: 'Đổi mật khẩu thành công!');
    }

    // ── Orders ───────────────────────────────────────────────
    public function filterOrders(string $status): void
    {
        $this->orderStatus = $status;
    }

    // ── Address ──────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetAddressForm();
        $this->showAddressModal = true;
    }

    public function openEdit(int $id): void
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId     = $id;
        $this->receiverName  = $address->receiver_name;
        $this->receiverPhone = $address->receiver_phone;
        $this->provinceName  = $address->province;
        $this->provinceCode  = $address->province_code ?? '';
        $this->wardName      = $address->ward;
        $this->wardCode      = $address->ward_code     ?? '';
        $this->addressLine   = $address->address_line;
        $this->isDefault     = (bool) $address->is_default;
        $this->showAddressModal = true;
    }

    public function closeModal(): void
    {
        $this->showAddressModal = false;
        $this->resetAddressForm();
    }

    public function saveAddress(): void
    {
        $this->validate([
            'receiverName'  => ['required', 'string', 'max:255'],
            'receiverPhone' => ['required', 'string', 'max:20'],
            'provinceName'  => ['required', 'string'],
            'wardName'      => ['required', 'string'],
            'addressLine'   => ['required', 'string', 'max:500'],
        ], [
            'receiverName.required'  => 'Vui lòng nhập họ tên.',
            'receiverPhone.required' => 'Vui lòng nhập số điện thoại.',
            'provinceName.required'  => 'Vui lòng chọn tỉnh/thành.',
            'wardName.required'      => 'Vui lòng chọn phường/xã.',
            'addressLine.required'   => 'Vui lòng nhập địa chỉ.',
        ]);

        $userId = Auth::id();

        if ($this->isDefault) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }

        $data = [
            'user_id'        => $userId,
            'receiver_name'  => $this->receiverName,
            'receiver_phone' => $this->receiverPhone,
            'province'       => $this->provinceName,
            'province_code'  => $this->provinceCode,
            'ward'           => $this->wardName,
            'ward_code'      => $this->wardCode,
            'address_line'   => $this->addressLine,
            'is_default'     => $this->isDefault,
        ];

        if ($this->editingId) {
            Address::where('user_id', $userId)->findOrFail($this->editingId)->update($data);
            $msg = 'Đã cập nhật địa chỉ!';
        } else {
            if (Address::where('user_id', $userId)->count() === 0) {
                $data['is_default'] = true;
            }
            Address::create($data);
            $msg = 'Đã thêm địa chỉ mới!';
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: $msg);
    }

    public function setDefault(int $id): void
    {
        $userId = Auth::id();
        Address::where('user_id', $userId)->update(['is_default' => false]);
        Address::where('user_id', $userId)->findOrFail($id)->update(['is_default' => true]);
        $this->dispatch('notify', type: 'success', message: 'Đã đặt địa chỉ mặc định!');
    }

    public function deleteAddress(int $id): void
    {
        Address::where('user_id', Auth::id())->findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Đã xóa địa chỉ!');
    }

    private function resetAddressForm(): void
    {
        $this->reset([
            'editingId', 'receiverName', 'receiverPhone',
            'provinceName', 'provinceCode', 'wardName', 'wardCode',
            'addressLine', 'isDefault',
        ]);
        $this->resetValidation();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $user = Auth::user();

        $ordersQuery = $user->invoices()->with(['items', 'payment', 'shipment'])->latest();
        if ($this->orderStatus) {
            $ordersQuery->where('status', $this->orderStatus);
        }
        $orders = $ordersQuery->paginate(5);

        $stats = [
            'total'     => $user->invoices()->count(),
            'active'    => $user->invoices()->whereIn('status', ['pending','confirmed','processing','shipped'])->count(),
            'delivered' => $user->invoices()->where('status', 'delivered')->count(),
        ];

        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get();

        return view('livewire.profile-page', compact('user', 'orders', 'stats', 'addresses'));
    }
}