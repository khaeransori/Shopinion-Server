<?php

use Zizaco\Entrust\EntrustRole;

/**
 * Role
 *
 * @property string $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Config::get('auth.model')[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\Config::get('entrust::permission')[] $perms
 * @property mixed $permissions
 * @method static \Illuminate\Database\Query\Builder|\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Role whereDeletedAt($value)
 */
class Role extends EntrustRole {

}