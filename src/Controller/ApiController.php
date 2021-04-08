<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/list", name="api_projects_list", methods={"GET"})
     */
    public function listProjects(Request $request, ProjectRepository $projectRepository)
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 10);
        $project = $projectRepository->findAll($offset, $limit);
        return $this->json($project, 200, [], [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'startedAt',
                'endedAt'
            ]
        ]);
    }
    
    /**
     * @Route("/api/project={id}", name="api_projects_tasks", methods={"GET"})
     */
    public function taskProject($id, Request $request, ProjectRepository $projectRepository)
    {
        $project = $projectRepository->find($id);
        return $this->json($project, 200, [], [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'startedAt',
                'endedAt',
                'tasks' => [
                    'title',
                    'description',
                    'createdAt'
                ]
            ]
        ]);
    }
}