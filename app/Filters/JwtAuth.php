<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\AuthController;

class JwtAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization'); // "Bearer xxx"
        if (! $header || ! preg_match('/Bearer\s(\S+)/', $header, $m)) {
            return service('response')->setStatusCode(401)->setJSON(['message'=>'Missing Bearer token']);
        }
        try {
            $claims = AuthController::decodeToken($m[1]);
            // simpan claims ke request (shared)
            $request->user = $claims;
        } catch (\Throwable $e) {
            return service('response')->setStatusCode(401)->setJSON(['message'=>'Invalid/expired token']);
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
