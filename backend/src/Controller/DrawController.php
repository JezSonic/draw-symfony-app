<?php

namespace App\Controller;

use App\Entity\Draw;
use App\Entity\DrawOption;
use App\Entity\DrawResult;
use App\Entity\DrawStatus;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use function is_array;

final class DrawController extends AbstractController
{
    #[Route('/draws', name: 'list_draws', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse {
        $draws = $em->getRepository(Draw::class)->findAll();
        $payload = [];
        foreach ($draws as $draw) {
            $payload[] = $this->buildResultPayload($draw);
        }
        return $this->json($payload);
    }

    /**
     * @throws RandomException
     */
    #[Route('/draws', name: 'create_draw', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid JSON body'], 400);
        }

        $name = $data['name'] ?? null;
        $resultsCount = $data['resultsCount'] ?? null;
        $options = $data['options'] ?? null;

        $errors = [];
        if (!\is_string($name) || trim($name) === '') {
            $errors['name'] = 'Name is required';
        }
        if (!\is_int($resultsCount) || $resultsCount < 1) {
            $errors['resultsCount'] = 'resultsCount must be a positive integer';
        }
        if (!is_array($options) || count($options) === 0) {
            $errors['options'] = 'options must be a non-empty array of strings';
        }

        if ($errors) {
            return $this->json(['errors' => $errors], 422);
        }

        $draw = new Draw($name, $resultsCount);
        foreach ($options as $optValue) {
            $option = new DrawOption($optValue['content'], $optValue['author']);
            $draw->addOption($option);
        }

        $em->persist($draw);
        $em->flush();

        return $this->json([
            'id' => $draw->id
        ], 201);
    }

    #[Route('/draws/{id}', name: 'get_draw', methods: ['GET'])]
    public function getOne(string $id, EntityManagerInterface $em): JsonResponse
    {
        if (trim($id) === '') {
            return $this->json(['error' => 'Invalid draw id'], 400);
        }

        /** @var Draw|null $draw */
        $draw = $em->getRepository(Draw::class)->find($id);
        if (!$draw) {
            return $this->json(['error' => 'Draw not found'], 404);
        }

        $options = [];
        foreach ($draw->options as $opt) {
            $options[] = [
                'id' => $opt->id,
                'content' => $opt->content,
                'author' => $opt->author,
            ];
        }

        return $this->json([
            'id' => $draw->id,
            'name' => $draw->name,
            'resultsCount' => $draw->resultsCount,
            'status' => $draw->status->name,
            'result' => $draw->getResult()?->getPayload() ?? [],
            'createdAt' => $draw->createdAt->format(DATE_ATOM),
            'finishedAt' => $draw->finishedAt?->format(DATE_ATOM),
            'options' => $options,
        ]);
    }

    /**
     * @throws RandomException
     */
    #[Route('/draws/{id}/run', name: 'run_draw', methods: ['POST'])]
    public function run(string $id, EntityManagerInterface $em): JsonResponse
    {
        if (trim($id) === '') {
            return $this->json(['error' => 'Invalid draw id'], 400);
        }

        /** @var Draw|null $draw */
        $draw = $em->getRepository(Draw::class)->find($id);
        if (!$draw) {
            return $this->json(['error' => 'Draw not found'], 404);
        }

        if ($draw->status === DrawStatus::FINISHED) {
            $existing = $this->buildResultPayload($draw);
            return $this->json($existing, 409);
        }

        $options = [];
        foreach ($draw->options as $opt) {
            $options[] = $opt;
        }

        if (count($options) === 0) {
            return $this->json(['error' => 'No options available to draw'], 422);
        }

        if ($draw->resultsCount > count($options)) {
            return $this->json([
                'error' => 'resultsCount exceeds number of available options',
                'availableOptions' => count($options),
                'requestedResults' => $draw->resultsCount,
            ], 422);
        }

        $indices = array_keys($options);
        shuffle($indices);
        $selectedIndices = array_slice($indices, 0, $draw->resultsCount);

        $winners = [];
        foreach ($selectedIndices as $idx) {
            $opt = $options[$idx];
            $winners[] = [
                'id' => $opt->id,
                'content' => $opt->content,
                'author' => $opt->author,
            ];
        }

        $result = new DrawResult($draw, ['winners' => $winners]);
        $draw->setResult($result);
        $draw->status = DrawStatus::FINISHED;
        $draw->finishedAt = new \DateTimeImmutable('now');

        $em->persist($result);
        $em->persist($draw);
        $em->flush();

        return $this->json([
            'id' => $draw->id,
            'status' => $draw->status->name,
            'finishedAt' => $draw->finishedAt?->format(DATE_ATOM),
            'result' => [
                'winners' => $winners,
            ],
        ]);
    }

    private function buildResultPayload(Draw $draw): array
    {
        $payload = [
            'id' => $draw->id,
            'name' => $draw->name,
            'resultsCount' => $draw->resultsCount,
            'createdAt' => $draw->createdAt->format(DATE_ATOM),
            'options' => $draw->options->toArray(),
            'result' => $draw->getResult()?->getPayload() ?? [],
            'status' => $draw->status->name,
            'finishedAt' => $draw->finishedAt?->format(DATE_ATOM),
        ];

        $maybe = $draw->getResult();
        if ($maybe && is_array($maybe->getPayload())) {
            $payload['result'] = $maybe->getPayload();
        }

        return $payload;
    }

}
