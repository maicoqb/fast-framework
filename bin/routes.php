<?php

use Symfony\Component\HttpFoundation\Response;

/** @var \Symfony\Component\HttpFoundation\Request $request */
/** @var Swift_Mailer $mailer */
/** @var Twig_Environment $twig */
/** @var \Particle\Validator\Validator $contactMailValidator */
$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) use ($contactMailValidator, $mailer, $request, $twig) {
        $r->addRoute(
            'GET',
            '/hello[/{name}]',
            function ($params) use ($request, $twig) {
                $name = $params['name'] ?? 'World';
                $response = new Response(
                    $twig->render('hello.twig', [
                        'name' => $name
                    ])
                );
                $response->send();
            }
        );
        $r->addRoute(
            'GET',
            '/goodbye[/{name}]',
            function ($params) use ($request, $twig) {
                $name = $params['name'] ?? 'World';
                $response = new Response(
                    $twig->render('goodbye.twig', [
                        'name' => $name
                    ])
                );
                $response->send();
            }
        );

        $r->addRoute(
            'GET',
            '/contact',
            function () use ($request, $twig) {
                $response = new Response(
                    $twig->render('contact.twig', [])
                );
                $response->send();
            }
        );
        $r->addRoute(
            'POST',
            '/contact',
            function () use ($mailer, $request, $twig, $contactMailValidator) {
                $result = $contactMailValidator->validate([
                    'message' => $request->get('message'),
                    'name' => $request->get('name'),
                    'email' => $request->get('email')
                ]);

                if ($result->isNotValid()) {
                    return new Response(
                        $twig->render('contact.twig', [
                            'errors' => $result->getMessages()
                        ])
                    );
                }

                $mailbody = $twig->render('contact-mail.twig', [
                    'message' => $request->get('message'),
                    'name' => $request->get('name'),
                    'email' => $request->get('email')
                ]);

                $message = (new Swift_Message('An e-mail from the site'))
                    ->setFrom([$request->get('email') => $request->get('name')])
                    ->setTo([getenv('CONTACT_EMAIL')])
                    ->setBody($mailbody);

                $mailer->send($message);


//                mail(
//                    getenv('CONTACT_EMAIL'),
//                    'An e-mail from the site',
//                    $mailbody
//                );

//                $response = new RedirectResponse('/hello');
//                $response->send();
            }
        );
    });
