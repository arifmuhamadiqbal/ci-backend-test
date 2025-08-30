<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $useSoftDeletes= true;
    protected $allowedFields = ['name','email','password','role'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
    protected $validationRules = [
        'name'  => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
    ];
}
