<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CsrfController extends AbstractController
{
    public const string TOKEN_ID = 'default';
    public const string COOKIE_NAME = 'XSRF-TOKEN';

    #[Route('/csrf/token', name: 'app_csrf_token', methods: ['GET'])]
    public function token(Request $request, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $token = $csrfTokenManager->getToken(self::TOKEN_ID)->getValue();

        $secure = $request->isSecure();
        $cookie = new Cookie(
            name: self::COOKIE_NAME,
            value: $token,
            expire: 0,
            path: '/',
            domain: null,
            secure: $secure,
            httpOnly: false,
            raw: false,
            sameSite: Cookie::SAMESITE_LAX
        );

        $response = new JsonResponse(null, 204);
        $response->headers->setCookie($cookie);

        return $response;
    }
}
