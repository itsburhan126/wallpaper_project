<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\ReferralHistory;
use App\Notifications\WelcomeNotification;
use App\Notifications\ReferralBonusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $signupBonus = (int) Setting::get('signup_bonus', 100);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => Str::upper(Str::random(8)),
                'avatar' => 'default.png',
                'coins' => 0,
                'coins' => $signupBonus,
                'referred_by' => $request->referral_code,
            ]);

            if ($request->referral_code) {
                $this->processReferral($user, $request->referral_code);
            }

            // Send Welcome Notification
            $user->notify(new WelcomeNotification());

            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processReferral($newUser, $referralCode)
    {
        // Level 1 (Direct Referrer)
        $l1Referrer = User::where('referral_code', $referralCode)->first();
        if ($l1Referrer) {
            $bonusL1 = (int) Setting::get('referral_bonus_l1', 50);
            $l1Referrer->increment('coins', $bonusL1);
            
            ReferralHistory::create([
                'referrer_id' => $l1Referrer->id,
                'referred_user_id' => $newUser->id,
                'level' => 1,
                'bonus_amount' => $bonusL1
            ]);

            // Record Transaction
            \App\Models\TransactionHistory::create([
                'user_id' => $l1Referrer->id,
                'type' => 'gem',
                'amount' => $bonusL1,
                'source' => 'referral_bonus',
                'description' => 'Referral Bonus (Level 1) from ' . $newUser->name,
            ]);

            // Level 2
            if ($l1Referrer->referred_by) {
                $l2Referrer = User::where('referral_code', $l1Referrer->referred_by)->first();
                if ($l2Referrer) {
                    $bonusL2 = (int) Setting::get('referral_bonus_l2', 20);
                    $l2Referrer->increment('coins', $bonusL2);

                    ReferralHistory::create([
                        'referrer_id' => $l2Referrer->id,
                        'referred_user_id' => $newUser->id,
                        'level' => 2,
                        'bonus_amount' => $bonusL2
                    ]);

                    \App\Models\TransactionHistory::create([
                        'user_id' => $l2Referrer->id,
                        'type' => 'gem',
                        'amount' => $bonusL2,
                        'source' => 'referral_bonus',
                        'description' => 'Referral Bonus (Level 2) from ' . $newUser->name,
                    ]);

                    // Level 3
                    if ($l2Referrer->referred_by) {
                        $l3Referrer = User::where('referral_code', $l2Referrer->referred_by)->first();
                        if ($l3Referrer) {
                            $bonusL3 = (int) Setting::get('referral_bonus_l3', 10);
                            $l3Referrer->increment('coins', $bonusL3);

                            ReferralHistory::create([
                                'referrer_id' => $l3Referrer->id,
                                'referred_user_id' => $newUser->id,
                                'level' => 3,
                                'bonus_amount' => $bonusL3
                            ]);

                            \App\Models\TransactionHistory::create([
                                'user_id' => $l3Referrer->id,
                                'type' => 'gem',
                                'amount' => $bonusL3,
                                'source' => 'referral_bonus',
                                'description' => 'Referral Bonus (Level 3) from ' . $newUser->name,
                            ]);

                            $l3Referrer->notify(new ReferralBonusNotification($newUser, 3, $bonusL3));
                        }
                    }
                }
            }
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->status === 'blocked') {
            return response()->json([
                'status' => false,
                'message' => 'Your account has been blocked. Please contact support.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 200);
    }

    public function googleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
            'avatar' => 'nullable|string',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();
            $isNewUser = false;

            if ($user && $user->status === 'blocked') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been blocked. Please contact support.'
                ], 403);
            }

            if (!$user) {
                $isNewUser = true;
                $signupBonus = (int) Setting::get('signup_bonus', 100);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $request->google_id,
                    'avatar' => $request->avatar ?? 'default.png',
                    'referral_code' => Str::upper(Str::random(8)),
                    'coins' => 100,
                    'coins' => $signupBonus,
                    'referred_by' => $request->referral_code,
                    'status' => 'active'
                ]);

                if ($request->referral_code) {
                    $this->processReferral($user, $request->referral_code);
                }
            } else {
                if ($request->avatar) {
                    $user->update(['avatar' => $request->avatar]);
                }
                if (!$user->google_id) {
                    $user->update(['google_id' => $request->google_id]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'is_new_user' => $isNewUser
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addReferrer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string|exists:users,referral_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();

        if ($user->referred_by) {
            return response()->json([
                'status' => false,
                'message' => 'You have already been referred by someone.'
            ], 400);
        }

        if ($user->referral_code === $request->referral_code) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot refer yourself.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $user->referred_by = $request->referral_code;
            $user->save();

            $this->processReferral($user, $request->referral_code);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Referral code applied successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to apply referral code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkUser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $exists = $user ? true : false;
        $isBlocked = $user && $user->status === 'blocked';
        
        return response()->json([
            'status' => true,
            'exists' => $exists,
            'is_blocked' => $isBlocked
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Delete user
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load recent referrals
        $referrals = ReferralHistory::where('referrer_id', $user->id)
            ->with('referredUser:id,name,avatar,created_at')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($history) {
                return [
                    'name' => $history->referredUser->name,
                    'avatar' => $history->referredUser->avatar,
                    'level' => $history->level,
                    'bonus' => $history->bonus_amount,
                    'date' => $history->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
                'referrals' => $referrals,
                'referral_count' => ReferralHistory::where('referrer_id', $user->id)->count(),
                'total_referral_earnings' => ReferralHistory::where('referrer_id', $user->id)->sum('bonus_amount'),
            ]
        ]);
    }
}
