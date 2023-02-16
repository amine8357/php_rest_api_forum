<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Forum;
 

#[Route(
    '/api',
    name: 'api_'
)]
class ForumController extends AbstractController
{

    #[Route(
        '/forum',
        name: 'forum_index',
        methods: ['GET','HEAD']

    )]
    public function index(ManagerRegistry $doctrine): Response
    {
        $forums = $doctrine
            ->getRepository(Forum::class)
            ->findAll();
  
        $data = [];
  
        foreach ($forums as $forum) {
           $data[] = [
               'name' => $forum->getName(),
               'description' => $forum->getDescription(),
               'fid'=> $forum->getFid(),
               'type'=> $forum->getType(),
               'linkto'=> $forum->getLinkto(),
               'dateCreation'=> $forum->getDateCreation(),
           ];
        }
  
  
        return $this->json($data);
    }
 
  

    #[Route(
        '/forum',
        name: 'forum_new',
        methods: ['POST']

    )]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
  
        $forum = new Forum();
        $forum->setName($request->request->get('name'));
        $forum->setDescription($request->request->get('description'));
        $forum->setFid($request->request->get('fid'));
        $forum->setType($request->request->get('type'));
        $forum->setLinkto($request->request->get('linkto'));
        $forum->setDateCreation(new \DateTime());

        $entityManager->persist($forum);
        $entityManager->flush();
  
        return $this->json('Created new forum successfully with id ' . $forum->getId());
    }
  
    /**
     * @Route("/forum/{id}", name="forum_show", methods={"GET"})
     */
    #[Route(
        '/forum/{id}',
        name: 'forum_show',
        methods: ['GET']

    )]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $forum = $doctrine->getRepository(Forum::class)->find($id);
  
        if (!$forum) {
  
            return $this->json('No forum found for id' . $id, 404);
        }
  
        $data =  [
            'id' => $forum->getId(),
            'name' => $forum->getName(),
            'description' => $forum->getDescription(),
        ];
          
        return $this->json($data);
    }
  

     #[Route(
        '/forum/{id}',
        name: 'forum_edit',
        methods: ['PUT']

    )]
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $forum = $entityManager->getRepository(Forum::class)->find($id);
  
        if (!$forum) {
            return $this->json('No forum found for id' . $id, 404);
        }
  
        $forum->setName($request->get('name'));
        $forum->setDescription($request->request->get('description'));
        $entityManager->flush();
  
        $data =  [
            'id' => $forum->getId(),
            'name' => $forum->getName(),
            'description' => $forum->getDescription(),
        ];
          
        return $this->json($data);
    }
  
     #[Route(
        '/forum/{id}',
        name: 'forum_delete',
        methods: ['DELETE']

    )]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $forum = $entityManager->getRepository(Forum::class)->find($id);
  
        if (!$forum) {
            return $this->json('No forum found for id' . $id, 404);
        }
  
        $entityManager->remove($forum);
        $entityManager->flush();
  
        return $this->json('Deleted a forum successfully with id ' . $id);
    }
}