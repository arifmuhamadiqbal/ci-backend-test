<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    private function jwtSecret() { return env('jwt.secret'); }
    private function jwtTTL()    { return (int) env('jwt.ttl'); }

    public function register()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = ['name'=>'required','email'=>'required|valid_email|is_unique[users.email]','password'=>'required|min_length[6]'];
        if (! $this->validate($rules)) return $this->response->setStatusCode(422)->setJSON(['errors'=>$this->validator->getErrors()]);

        $userM = new UserModel();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $userM->insert($data);
        return $this->response->setStatusCode(201)->setJSON(['message'=>'registered']);
    }

    public function login()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $user = (new UserModel())->where('email', $data['email'] ?? '')->first();
        if (! $user || ! password_verify($data['password'] ?? '', $user['password']))
            return $this->response->setStatusCode(401)->setJSON(['message'=>'Invalid credentials']);

        $payload = [
            'iss' => base_url(),
            'sub' => $user['id'],
            'name'=> $user['name'],
            'role'=> $user['role'],
            'iat' => time(),
            'exp' => time() + $this->jwtTTL(),
        ];
        $token = JWT::encode($payload, $this->jwtSecret(), 'HS256');
        return $this->response->setJSON(['token'=>$token,'expires_in'=>$this->jwtTTL()]);
    }

    // helper untuk verifikasi token (dipakai Filter juga)
    public static function decodeToken(string $jwt)
    {
        return (array) JWT::decode($jwt, new Key(env('jwt.secret'), 'HS256'));
    }
}
