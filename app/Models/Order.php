<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'external_reference',
        'status',
        'total',
        'currency',
        'buyer_name',
        'buyer_email',
        'payment_id',
        'payment_method',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Aprobado',
            'pending' => 'Pendiente',
            'rejected' => 'Rechazado',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusClasses(): string
    {
        return match ($this->status) {
            'approved' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'rejected' => 'bg-rose-100 text-rose-700 ring-rose-200',
            default => 'bg-stone-100 text-stone-600 ring-stone-200',
        };
    }
}
