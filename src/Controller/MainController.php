<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class MainController extends AbstractController
{
private $em;
public function __construct(EntityManagerInterface $em)
{
    $this->em = $em;
}

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('main/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/create-post', name: 'create-post')]
    public function create_post(Request $request)
    {
      $post =new Post();
      $form = $this->createForm(PostType::class,$post);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
      $this->em->persist($post);
      $this->em->flush();
       $this->addFlash('message','Inserted Successfully');
      return $this->redirectToRoute('app_main');
      }
      return $this->render('main/post.html.twig',[
        'form' => $form->createView(),
         'button_label' => 'Create Post'
      ]);
    }

    #[Route('/edit-post/{id}', name: 'edit-post')]
    public function edit_post(Request $request,$id){
        $post = $this->em->getRepository(Post::class)->find($id);
        $form = $this->createForm(PostType::class,$post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($post);
            $this->em->flush();
             $this->addFlash('message','Edited Successfully');
            return $this->redirectToRoute('app_main');
            }
            return $this->render('main/post.html.twig',[
              'form' => $form->createView(),
              'button_label' => 'Edit Post'
            ]);

    }
    #[Route('/delete-post/{id}', name: 'delete-post')]
    public function delete_post($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $this->em->remove($post);
        $this->em->flush();
        $this->addFlash('message','Deleted Successfully');
        return $this->redirectToRoute('app_main');
   
    }
}
