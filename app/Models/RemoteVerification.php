<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteVerification extends Model
{
    // app/Models/RemoteVerification.php
protected $fillable = ['election_id', 'voter_id', 'verification_code', 'upload_token_hash', 'upload_token_expires_at', 'upload_uploaded_at', 'ktp_photo_path', 'selfie_photo_path', 'consent_accepted', 'consent_at', 'status', 'verified_by_1', 'verified_by_2', 'verified_at', 'reject_reason', 'expires_at'];
protected $hidden = ['ktp_photo_path', 'selfie_photo_path']; // path file sensitif, jangan ikut ter-expose di JSON

public function election() { return $this->belongsTo(Election::class); }
public function voter() { return $this->belongsTo(Voter::class); }
public function verifier1() { return $this->belongsTo(User::class, 'verified_by_1'); }
public function verifier2() { return $this->belongsTo(User::class, 'verified_by_2'); }
public function votingToken() { return $this->hasOne(VotingToken::class); }
}
