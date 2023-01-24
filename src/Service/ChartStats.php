<?php

namespace App\Service;

use App\Repository\CommentRepository;
use App\Repository\IdeaRepository;
use App\Repository\ProjectRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartStats
{
    private ChartBuilderInterface $chartBuilder;
    private ProjectRepository $projectRepository;
    private IdeaRepository $ideaRepository;
    private CommentRepository $commentRepository;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        ProjectRepository $projectRepository,
        IdeaRepository $ideaRepository,
        CommentRepository $commentRepository
    ){
        $this->chartBuilder = $chartBuilder;
        $this->projectRepository = $projectRepository;
        $this->ideaRepository = $ideaRepository;
        $this->commentRepository = $commentRepository;
    }

    public function getMobileProjectChart1(): object
    {
        $nbProjects = $this->projectRepository->countNumberProjects();
        $nbIdeas = $this->ideaRepository->countNumberIdeas();
        $nbProjectViews = $this->projectRepository->countTotalProjectViews();
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Projects','Ideas', 'Views'],
            'datasets' => [
                [
                    'barThickness' => 50,
                    'backgroundColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'borderColor' => [
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)'
                    ],
                    'data' => [
                        $nbProjects[0]['nbProjects'],
                        $nbIdeas[0]['nbIdeas'],
                        $nbProjectViews[0]['nbProjectViews']
                    ]
                ],
            ],
        ]);
        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false
                ],
            ],
        ]);

        return $chart;
    }

    public function getMobileIdeaChart1(): object
    {
        $nbComments = $this->commentRepository->countComments();
        $nbIdeaViews = $this->ideaRepository->countTotalIdeaViews();
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['Comments', 'Likes', 'Views'],
            'datasets' => [
                [
                    'barThickness' => 50,
                    'backgroundColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'borderColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'barPercentage' => 0.1,
                    'data' => [
                        $nbComments[0]['nbComments'],
                        91,
                        $nbIdeaViews[0]['nbIdeaViews']
                    ]
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false
                ]
            ],
        ]);

        return $chart;
    }

    public function getMobileProjectChart2(): object
    {
        $nbProjects = $this->projectRepository->countNumberProjects();
        $nbIdeas = $this->ideaRepository->countNumberIdeas();
        $nbProjectViews = $this->projectRepository->countTotalProjectViews();
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => ['Projects', 'Ideas', 'Views'],
            'datasets' => [
                [
                    'barThickness' => 70,
                    'hoverOffset' => 7,
                    'backgroundColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'borderColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'data' => [
                        $nbProjects[0]['nbProjects'],
                        $nbIdeas[0]['nbIdeas'],
                        $nbProjectViews[0]['nbProjectViews']
                    ]
                ],
            ],
        ]);

        return $chart;
    }

    public function getMobileIdeaChart2(): object
    {
        $nbComments = $this->commentRepository->countComments();
        $nbIdeaViews = $this->ideaRepository->countTotalIdeaViews();
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => ['Comments', 'Likes', 'Views'],
            'datasets' => [
                [
                    'barThickness' => 70,
                    'hoverOffset' => 7,
                    'backgroundColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'borderColor' => [
                        'rgba(7, 200, 255, 0.4)',
                        'rgba(0, 66, 191, 0.4)',
                        'rgba(192, 16, 255, 0.4)'
                    ],
                    'data' => [
                        $nbComments[0]['nbComments'],
                        91,
                        $nbIdeaViews[0]['nbIdeaViews']
                    ]
                ],
            ],
        ]);

        return $chart;
    }
}
