<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends AbstractController
{

    //************************ Liste des projets ************************************************** */

    /**
     * @Route("/project/list", name="project_list")
     */
    public function list(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->render('project/project_list.html.twig', [
            'projects' => $projects
        ]);
    }

    //************************ Création d'un projet ************************************************** */

    /**
     * @Route("/project/create", name="project_creation")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
    {
        if ($request->getMethod() == 'GET') {
            return $this->render('project/project_creation.html.twig');
        }
        if ($request->getMethod() == 'POST') {
            $project = new Project();
            $project->setName($request->request->get('name'));
            $project->setStartedAt(new \DateTime());
            $project->setProjectStatus('Nouveau');

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_list');
        }
    }


    //************************ Gestion d'un projet ************************************************** */

    /**
     * @Route("/project{id}", name="project_manage")
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage($id,
                         Request $request,
                         ProjectRepository $projectRepository,
                         EntityManagerInterface $entityManager)
    {
        $project = $projectRepository->find($id);
        if ($project === null) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() == 'GET') {
            return $this->render('project/project_manage.html.twig', [
                'project' => $project,
            ]);
        }

        if ($project->getProjectStatus() != "Terminé"){
            if ($request->getMethod() == 'POST') {
                $result = $request->request->get('status');
                $project->setProjectStatus($result);
                //si le projet est terminé, alors on rentre la date de fin dans la BDD
                if ($result == "Terminé")
                {
                    $project->setEndedAt(new \DateTime());
                }

                $entityManager->persist($project);
                $entityManager->flush();
                return $this->redirectToRoute('project_manage', ['id' => $id]);
            }
        }
    }

    //************************ Ajout d'une tâche à un projet ************************************************** */

    /**
     * @Route("/project{id}/addTask", name="project_add_task")
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTask($id,
                         Request $request,
                         ProjectRepository $projectRepository,
                         EntityManagerInterface $entityManager)
    {
        $project = $projectRepository->find($id);
        if ($project === null) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() == 'GET') {
            return $this->render('project/project_add_task.html.twig', [
                'project' => $project,
            ]);
        }

        if ($request->getMethod() == 'POST') {
            $task = new Task();
            

            $task->setTitle($request->request->get('title'));
            $task->setDescription($request->request->get('description'));
            $task->setCreatedAt(new \DateTime());
            $task->setProject($project);
            

            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('project_manage', ['id' => $id]);
        }

    }

}    