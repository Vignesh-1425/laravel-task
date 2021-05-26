<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
	use Notifiable;

    protected $table 		= 'users';
	protected $primaryKey 	= 'id';
	
	protected $fillable = [
		'username', 'title', 'firstname', 'lastname', 'gender', 'date_of_birth', 'email', 'phone', 'email_verified_at', 'profile_photo', 'availability',
		'nhs_number', 'address', 'postcode', 'emergency_contact', 'gp_details', 'occupation', 'height', 'weight', 'blood_type', 'referral_source', 'medication_allergies',
		'current_medication', 'more_info', 'slot', 'communication_preference', 'privacy_policy', 'patient_note', 'stripe_user_id', 'stripe_response', 'specialism', 'qualifications', 'med_co_no',
		'mdu_no', 'qualifications_details', 'gp_med_co_legal_experience', 'med_co_legal_experience', 'signature', 'role_id', 'role_type', 'user_id', 'creator_id', 'room_id', 'deleted_at',
		'password', 'remember_token', 'gmc_no'
	];

	
	protected $hidden = [
		'password'
	];

	public function getJWTIdentifier()
	{
		return $this->getKey();
	}
	
	public function getJWTCustomClaims()
	{
		return [];
	}
	
	public function getCapTitleAttribute(): string
    {
        return ucfirst($this->title);
    }

    public function getFullNameAttribute(): string
    {
        $date_of_birth = $this->date_of_birth != null ? ' DOB : ' . $this->date_of_birth : '';
        $nhs_number = $this->nhs_number != null ? ' - NHS : ' . $this->nhs_number : '';

        return ucfirst($this->firstname) . ' ' . ucfirst($this->lastname) . $date_of_birth . $nhs_number;
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function drugs()
    {
        return $this->hasMany(Drug::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class)->with('items');
    }

    public function doctorInvoices()
    {
        return $this->hasMany(Invoice::class, 'doctor_id')->with('items');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function sicknotes()
    {
        return $this->hasMany(Sicknote::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function paymentmethods()
    {
        return $this->hasMany(Paymentmethod::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, ['role_id', 'role_type'], ['role_id', 'role_type']);
    }

    public function initialconsultations()
    {
        return $this->hasMany(InitialConsultation::class);
    }

    public function followupconsultations()
    {
        return $this->hasMany(FollowupConsultation::class);
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function timings()
    {
        return $this->hasMany(Doctortiming::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function timing()
    {
        return $this->hasMany(Timing::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function services()
    {
		return $this->belongsToMany(Service::class);
    }

    public function companies()
    {
        return $this->belongsToMany(CompanyDetail::class);
    }

    public function company()
    {
        return $this->hasOne(CompanyDetailUser::class, 'user_id', 'id');
    }

    public function test()
    {
        return $this->hasOne('App\CompanyDetail');
    }

    public function templates()
    {
        return $this->hasMany('App\Template');
    }

    public function notes()
    {
        return $this->hasMany('App\PatientTreatmentNote');
    }

    public function treatmentNote()
    {
        return $this->hasMany('App\PatientTreatment');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function smss()
    {
        return $this->hasMany('App\Sms');
    }

    public function patients()
    {
        return $this->hasMany('App\User', 'user_id');
    }

    public function medicalReports()
    {
        return $this->hasMany('App\MedicalReport');
    }

    public function smartNotes()
    {
        return $this->hasMany(PatientSmartNote::class);
    }

    public function records()
    {
        return $this->hasMany(AudioRecord::class);
    }
	
}
