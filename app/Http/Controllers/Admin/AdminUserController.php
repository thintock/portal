<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * ユーザー一覧
     */
    public function index()
    {
        $users = \App\Models\User::with('avatar') // アバター画像を eager load
            ->paginate(20);
    
        return view('admin.users.index', compact('users'));
    }

    /**
     * ユーザー編集フォーム
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'first_name_kana' => 'nullable|string|max:50',
            'last_name_kana' => 'nullable|string|max:50',
            'display_name' => 'nullable|string|max:50',
            'instagram_id' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'prefecture' => 'nullable|string|max:50',
            'address1' => 'nullable|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
            'country' => 'nullable|string|size:2',
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|max:20',
            'user_type' => 'nullable|string|max:10',
            'user_status' => 'nullable|string|max:20',
            'email_notification' => 'boolean',
            'remarks' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);
    
        $user->fill($data);
        $user->save();
    
        return redirect()->route('admin.users.index')->with('success', 'ユーザーを更新しました');
    }



    /**
     * ユーザー削除処理
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを削除しました。');
    }
}
