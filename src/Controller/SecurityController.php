<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserLoginFormType;
use App\Form\UserRegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();
        $loginForm = $this->createForm(UserLoginFormType::class);

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'loginForm'     => $loginForm->createView(),
        ));
    }

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $request->getSession()->clear();
        //dump($request);
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/register", name="register")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $registrationForm = $this->createForm(UserRegistrationFormType::class);
        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();//pour communiquer avec la bdd
            $data = $registrationForm->getData(); //Pour récupérer les données
            //$passwordEncoder = $this->get('security.password_encoder');

            $user = new User();
            $user->setUsername($data['username'])
                 ->setEmail($data['email'])
                 //->setPassword($passwordEncoder->encodePassword($user, $data['password']));
                 ->setPlainPassword($data['password']);

            $em->persist($user);    // pour les nouveaux éléments uniquement
            $em->flush();           // pour envoyer
            //dump($registrationForm);

            return $this->redirectToRoute('homepage');

        }
        //dump($request);

        // replace this line with your own code!
        //return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);

        //['registrationForm' => $registrationForm,] OU compact($registrationForm)
        return $this->render('security/register.html.twig',[
            'registrationForm' => $registrationForm->createView(),
        ]);
    }
}
