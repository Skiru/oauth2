<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Model\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomepageController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $userModel = new UserModel();
        $form = $this->createForm(RegistrationFormType::class, $userModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $user->setPassword($passwordEncoder->encodePassword(
               $user,
               $form->get('plainPassword')->getData()
            ));
            $user
                ->setFirstName($userModel->getFirstName())
                ->setLastName($userModel->getLastName())
                ->setEmail($userModel->getEmail());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->redirectToRoute('cleaning_homepage');
        }

        return $this->render('homepage.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
}