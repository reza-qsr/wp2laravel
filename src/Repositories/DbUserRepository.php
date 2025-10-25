<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use Illuminate\Support\Str;
use RezaQsr\Wp2Laravel\Contracts\UserRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\User;
use RezaQsr\Wp2Laravel\Models\UserMeta;
use RezaQsr\Wp2Laravel\Support\PasswordHasher;

class DbUserRepository implements UserRepositoryInterface
{
    public function insert(array $data)
    {
        $hasher = new PasswordHasher(8, true);

        $userLogin = $data['user_login'] ?? null;
        $userEmail = $data['user_email'] ?? null;
        $userPass = $data['user_pass'] ?? null;

        if (empty($userLogin)) {
            throw new \InvalidArgumentException('user_login is required.');
        }

        if (User::where('user_login', $userLogin)->exists()) {
            throw new \RuntimeException('Username already exists.');
        }

        if (!empty($userEmail) && User::where('user_email', $userEmail)->exists()) {
            throw new \RuntimeException('Email already exists.');
        }

        $defaults = [
            'user_pass' => $userPass
                ? $hasher->HashPassword($userPass)
                : $hasher->HashPassword(str()->random(12)),
            'user_nicename' => $data['user_nicename'] ?? $userLogin,
            'user_email' => $userEmail ?? '',
            'user_url' => $data['user_url'] ?? '',
            'user_registered' => now(),
            'user_activation_key' => '',
            'display_name' => $data['display_name'] ?? $userLogin,
        ];

        $data = array_merge($defaults, $data);

        $user = User::create($data);

        if (!empty($user->ID)) {
            $this->updateMeta($user->ID, 'wp_capabilities', ['subscriber' => true]);
            $this->updateMeta($user->ID, 'wp_user_level', 0);
        }

        return $user;
    }
    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }

        $updateData = [];

        $allowedFields = [
            'user_login',
            'user_email',
            'display_name',
            'user_nicename',
            'user_url'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (!empty($data['user_pass'])) {
            $hasher = new PasswordHasher(8, true);
            $updateData['user_pass'] = $hasher->HashPassword($data['user_pass']);
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return true;
    }
    public function delete(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        $user->metas()->delete();
        return $user->delete() > 0;
    }

    public function getMeta(int $userId, string $key, $default = null)
    {
        $meta = UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->first();
        return $meta ? $this->maybeUnserialize($meta->meta_value) : $default;
    }
    public function hasMeta(int $userId, string $key): bool
    {
        return UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->exists();
    }
    public function updateMeta(int $userId, string $key, $value): bool
    {
        $meta = UserMeta::updateOrCreate(
            ['user_id' => $userId, 'meta_key' => $key],
            ['meta_value' => $this->maybeSerialize($value)]
        );
        return (bool)$meta;
    }
    public function deleteMeta(int $userId, string $key): bool
    {
        return UserMeta::where('user_id', $userId)
                ->where('meta_key', $key)
                ->delete() > 0;
    }

    public function getRoles(int $userId): array
    {
        $caps = $this->getMeta($userId, 'wp_capabilities', []);
        return is_array($caps) ? array_keys(array_filter($caps)) : [];
    }


    protected function maybeSerialize($value)
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string)$value;
    }
    protected function maybeUnserialize($value)
    {
        $maybe = @unserialize($value);
        if ($maybe === false && $value !== 'b:0;') {
            return $value;
        }
        return $maybe;
    }
}
