<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'reporter_id',
        'reason',
        'details',
        'contact_email',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->reportable_type) {
            User::class => 'Usuari',
            Message::class => 'Missatge',
            Route::class => 'Ruta',
            Event::class => 'Quedada',
            SaleListing::class => 'Venda',
            default => class_basename($this->reportable_type),
        };
    }

    public function getSubjectLabelAttribute(): string
    {
        $subject = $this->reportable;

        if (!$subject) {
            return 'Contingut eliminat';
        }

        return match ($this->reportable_type) {
            User::class => $subject->name,
            Message::class => str($subject->body)->limit(60)->toString(),
            Route::class, Event::class, SaleListing::class => $subject->title,
            default => "#{$this->reportable_id}",
        };
    }
}
