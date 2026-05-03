<?php

namespace App\Livewire;

use App\Models\Address;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AddressManager extends Component
{
    public $editingId     = null;
    public $showForm      = false;
    public $receiverName  = '';
    public $receiverPhone = '';
    public $addressLine   = '';
    public $provinceName  = '';
    public $provinceCode  = '';
    public $wardName      = '';
    public $wardCode      = '';
    public $isDefault     = false;

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $this->editingId     = $id;
        $this->receiverName  = $address->receiver_name;
        $this->receiverPhone = $address->receiver_phone;
        $this->addressLine   = $address->address_line;
        $this->provinceName  = $address->province;
        $this->provinceCode  = $address->province_code;
        $this->wardName      = $address->ward;
        $this->wardCode      = $address->ward_code;
        $this->isDefault     = $address->is_default;
        $this->showForm      = true;
    }

    public function save(): void
    {
        $this->validate([
            'receiverName'  => 'required|string|max:255',
            'receiverPhone' => 'required|string|max:20',
            'addressLine'   => 'required|string|max:255',
            'provinceName'  => 'required|string',
            'wardName'      => 'required|string',
            'provinceCode'  => 'required',
            'wardCode'      => 'required',
        ], [
            'provinceName.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'wardName.required'     => 'Vui lòng chọn phường/xã.',
            'provinceCode.required' => 'Vui lòng chọn tỉnh/thành phố.',
            'wardCode.required'     => 'Vui lòng chọn phường/xã.',
        ]);

        if ($this->isDefault) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $data = [
            'receiver_name'  => $this->receiverName,
            'receiver_phone' => $this->receiverPhone,
            'address_line'   => $this->addressLine,
            'province'       => $this->provinceName,
            'province_code'  => (string) $this->provinceCode,
            // API v2: ward name lưu thẳng, không kèm tên huyện
            'ward'           => $this->wardName,
            'ward_code'      => (string) $this->wardCode,
            'is_default'     => $this->isDefault,
        ];

        if ($this->editingId) {
            auth()->user()->addresses()->findOrFail($this->editingId)->update($data);
        } else {
            if (auth()->user()->addresses()->count() === 0) {
                $data['is_default'] = true;
            }
            auth()->user()->addresses()->create($data);
        }

        $this->resetForm();
        $this->showForm = false;
        session()->flash('success', $this->editingId ? 'Đã cập nhật địa chỉ!' : 'Đã thêm địa chỉ mới!');
    }

    public function setDefault(int $id): void
    {
        auth()->user()->addresses()->update(['is_default' => false]);
        auth()->user()->addresses()->findOrFail($id)->update(['is_default' => true]);
    }

    public function delete(int $id): void
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            auth()->user()->addresses()->first()?->update(['is_default' => true]);
        }
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId     = null;
        $this->receiverName  = '';
        $this->receiverPhone = '';
        $this->addressLine   = '';
        $this->provinceName  = '';
        $this->provinceCode  = '';
        $this->wardName      = '';
        $this->wardCode      = '';
        $this->isDefault     = false;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.address-manager', [
            'addresses' => auth()->user()->addresses()->orderBy('is_default', 'desc')->get(),
        ]);
    }
}